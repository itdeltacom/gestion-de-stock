<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'reference',
        'barcode',
        'description',
        'category_id',
        'tva_rate',
        'price',
        'current_average_cost',
        'stock_method',
        'alert_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tva_rate' => 'decimal:2',
            'price' => 'decimal:2',
            'current_average_cost' => 'decimal:2',
            'alert_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'reference', 'barcode', 'category_id', 'tva_rate', 'price', 'stock_method', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Générer le code si non fourni
            if (empty($product->code)) {
                $lastProduct = self::orderBy('code', 'desc')->first();
                
                if ($lastProduct) {
                    $lastNumber = (int) substr($lastProduct->code, 3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $product->code = 'PRD' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            }

            // Générer le code-barres à partir de la référence
            if (!empty($product->reference) && empty($product->barcode)) {
                $product->barcode = self::generateBarcodeFromReference($product->reference);
            }
        });

        static::updating(function ($product) {
            // Regénérer le code-barres si la référence change
            if ($product->isDirty('reference') && !empty($product->reference)) {
                $product->barcode = self::generateBarcodeFromReference($product->reference);
            }
        });
    }

    /**
     * Générer un code-barres EAN-13 à partir de la référence interne
     * Format: 200 (préfixe interne) + 9 chiffres de la référence + 1 chiffre de contrôle
     */
    public static function generateBarcodeFromReference($reference)
    {
        // Convertir la référence en chiffres (enlever les caractères non numériques)
        $numericReference = preg_replace('/[^0-9]/', '', $reference);
        
        // Si la référence contient moins de 9 chiffres, compléter avec des zéros
        $numericReference = str_pad($numericReference, 9, '0', STR_PAD_LEFT);
        
        // Prendre seulement les 9 premiers chiffres
        $numericReference = substr($numericReference, 0, 9);
        
        // Préfixe 200 (code interne pour identifier nos produits)
        $barcode = '200' . $numericReference;
        
        // Calculer le chiffre de contrôle EAN-13
        $checkDigit = self::calculateEAN13CheckDigit($barcode);
        
        return $barcode . $checkDigit;
    }

    /**
     * Calculer le chiffre de contrôle EAN-13
     */
    private static function calculateEAN13CheckDigit($code)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$code[$i] * (($i % 2 === 0) ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit;
    }

    /**
     * Méthode pour regénérer manuellement le code-barres
     */
    public function regenerateBarcode()
    {
        if (!empty($this->reference)) {
            $this->barcode = self::generateBarcodeFromReference($this->reference);
            $this->save();
        }
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class)->orderBy('date', 'desc');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function stockFifo()
    {
        return $this->hasMany(StockFifo::class);
    }

    // Méthodes utiles
    public function getTotalStock()
    {
        return $this->stocks()->sum('quantity');
    }

    public function getStockByWarehouse($warehouseId)
    {
        return $this->stocks()->where('warehouse_id', $warehouseId)->first()?->quantity ?? 0;
    }

    public function isLowStock()
    {
        return $this->getTotalStock() <= $this->alert_stock;
    }

    public function getPriceWithTVA()
    {
        return $this->price * (1 + ($this->tva_rate / 100));
    }

    public function getMargin()
    {
        if ($this->current_average_cost == 0) {
            return 0;
        }
        return (($this->price - $this->current_average_cost) / $this->current_average_cost) * 100;
    }

    // Calcul du CMUP
    public function updateAverageCost($newQuantity, $newCost)
    {
        $currentTotalStock = $this->getTotalStock();
        $currentTotalValue = $currentTotalStock * $this->current_average_cost;
        
        $newTotalValue = $currentTotalValue + ($newQuantity * $newCost);
        $newTotalStock = $currentTotalStock + $newQuantity;
        
        if ($newTotalStock > 0) {
            $this->current_average_cost = $newTotalValue / $newTotalStock;
            $this->save();
        }
    }
}