<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Purchase extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'supplier_id',
        'warehouse_id',
        'user_id',
        'purchase_date',
        'total_ht',
        'total_tva',
        'total_ttc',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'total_ht' => 'decimal:2',
            'total_tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'supplier_id', 'warehouse_id', 'purchase_date', 'total_ttc', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->reference)) {
                $year = date('Y');
                $month = date('m');
                $lastPurchase = self::where('reference', 'like', "ACH-{$year}{$month}-%")
                    ->orderBy('reference', 'desc')
                    ->first();
                
                if ($lastPurchase) {
                    $lastNumber = (int) substr($lastPurchase->reference, -5);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $purchase->reference = "ACH-{$year}{$month}-" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });

        // Mettre à jour le stock quand l'achat est reçu
        static::updated(function ($purchase) {
            if ($purchase->isDirty('status') && $purchase->status === 'recu') {
                $purchase->updateStock();
            }
        });
    }

    // Relations
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function stockFifo()
    {
        return $this->hasMany(StockFifo::class);
    }

    // Méthodes utiles
    public function calculateTotals()
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($this->details as $detail) {
            $totalHt += $detail->unit_price * $detail->quantity;
            $totalTva += $detail->tva_amount;
        }

        $this->total_ht = $totalHt;
        $this->total_tva = $totalTva;
        $this->total_ttc = $totalHt + $totalTva;
        $this->save();
    }

    public function updateStock()
    {
        foreach ($this->details as $detail) {
            $product = $detail->product;
            
            // Trouver ou créer le stock pour ce warehouse
            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->warehouse_id,
                ],
                [
                    'quantity' => 0,
                    'average_cost' => 0,
                ]
            );

            // Mise à jour selon la méthode de stock
            if ($product->stock_method === 'cmup') {
                // Calcul CMUP
                $stock->addStock($detail->quantity, $detail->unit_price);
                
                // Mettre à jour le CMUP global du produit
                $product->updateAverageCost($detail->quantity, $detail->unit_price);
            } else {
                // Méthode FIFO
                $stock->quantity += $detail->quantity;
                $stock->save();

                // Créer une entrée FIFO
                StockFifo::create([
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->warehouse_id,
                    'purchase_id' => $this->id,
                    'quantity_in' => $detail->quantity,
                    'quantity_remaining' => $detail->quantity,
                    'unit_cost' => $detail->unit_price,
                    'entry_date' => $this->purchase_date,
                ]);
            }

            // Enregistrer l'historique des prix
            PriceHistory::create([
                'product_id' => $detail->product_id,
                'supplier_id' => $this->supplier_id,
                'purchase_price' => $detail->unit_price,
                'sale_price' => $product->price,
                'average_cost' => $product->current_average_cost,
                'date' => $this->purchase_date,
                'note' => "Achat: {$this->reference}",
            ]);
        }
    }

    public function canBeDeleted()
    {
        return $this->status === 'en_attente';
    }
}