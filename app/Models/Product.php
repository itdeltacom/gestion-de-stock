<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
        'featured_image',  // Ajoutez ceci
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

            if (!empty($product->reference) && empty($product->barcode)) {
                $product->barcode = self::generateBarcodeFromReference($product->reference);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('reference') && !empty($product->reference)) {
                $product->barcode = self::generateBarcodeFromReference($product->reference);
            }
        });

        static::deleting(function ($product) {
            // Delete featured image
            if ($product->featured_image && Storage::exists($product->featured_image)) {
                Storage::delete($product->featured_image);
            }

            // Delete gallery images
            foreach ($product->images as $image) {
                if (Storage::exists($image->image_path)) {
                    Storage::delete($image->image_path);
                }
                $image->delete();
            }
        });
    }

    public static function generateBarcodeFromReference($reference)
    {
        $numericReference = preg_replace('/[^0-9]/', '', $reference);
        $numericReference = str_pad($numericReference, 9, '0', STR_PAD_LEFT);
        $numericReference = substr($numericReference, 0, 9);
        $barcode = '200' . $numericReference;
        $checkDigit = self::calculateEAN13CheckDigit($barcode);
        return $barcode . $checkDigit;
    }

    private static function calculateEAN13CheckDigit($code)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $code[$i] * (($i % 2 === 0) ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit;
    }

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

    // AJOUTEZ CETTE RELATION
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // Image Methods
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image && Storage::disk('public')->exists($this->featured_image)) {
            return Storage::url($this->featured_image);
        }
        return asset('assets/img/no-image.svg');
    }

    public function deleteFeaturedImage()
    {
        if ($this->featured_image && Storage::disk('public')->exists($this->featured_image)) {
            Storage::disk('public')->delete($this->featured_image);
            $this->featured_image = null;
            $this->save();
        }
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