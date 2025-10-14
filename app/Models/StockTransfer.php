<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockTransfer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'from_warehouse_id',
        'to_warehouse_id',
        'user_id',
        'transfer_date',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'from_warehouse_id', 'to_warehouse_id', 'transfer_date', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->reference)) {
                $year = date('Y');
                $month = date('m');
                $lastTransfer = self::where('reference', 'like', "TRF-{$year}{$month}-%")
                    ->orderBy('reference', 'desc')
                    ->first();
                
                if ($lastTransfer) {
                    $lastNumber = (int) substr($lastTransfer->reference, -5);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $transfer->reference = "TRF-{$year}{$month}-" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });

        // Mettre à jour le stock quand le transfert est reçu
        static::updated(function ($transfer) {
            if ($transfer->isDirty('status')) {
                if ($transfer->status === 'envoye') {
                    $transfer->reduceFromWarehouseStock();
                } elseif ($transfer->status === 'recu') {
                    $transfer->addToWarehouseStock();
                }
            }
        });
    }

    // Relations
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }

    // Méthodes utiles
    public function reduceFromWarehouseStock()
    {
        foreach ($this->details as $detail) {
            $stock = Stock::where('product_id', $detail->product_id)
                ->where('warehouse_id', $this->from_warehouse_id)
                ->first();

            if (!$stock || $stock->quantity < $detail->quantity) {
                throw new \Exception("Stock insuffisant pour le produit: {$detail->product->name}");
            }

            $stock->reduceStock($detail->quantity);
        }
    }

    public function addToWarehouseStock()
    {
        foreach ($this->details as $detail) {
            $product = $detail->product;
            
            // Récupérer le coût moyen depuis l'entrepôt source
            $sourceStock = Stock::where('product_id', $detail->product_id)
                ->where('warehouse_id', $this->from_warehouse_id)
                ->first();
            
            $averageCost = $sourceStock ? $sourceStock->average_cost : $product->current_average_cost;
            
            // Trouver ou créer le stock pour l'entrepôt de destination
            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->to_warehouse_id,
                ],
                [
                    'quantity' => 0,
                    'average_cost' => 0,
                ]
            );

            if ($product->stock_method === 'cmup') {
                $stock->addStock($detail->quantity, $averageCost);
            } else {
                // FIFO
                $stock->quantity += $detail->quantity;
                $stock->save();

                StockFifo::create([
                    'product_id' => $detail->product_id,
                    'warehouse_id' => $this->to_warehouse_id,
                    'purchase_id' => null,
                    'quantity_in' => $detail->quantity,
                    'quantity_remaining' => $detail->quantity,
                    'unit_cost' => $averageCost,
                    'entry_date' => $this->transfer_date,
                ]);
            }
        }
    }

    public function canBeDeleted()
    {
        return $this->status === 'en_attente';
    }

    public function canBeSent()
    {
        return $this->status === 'en_attente';
    }

    public function canBeReceived()
    {
        return $this->status === 'envoye';
    }
}