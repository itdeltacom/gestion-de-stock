<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockFifo extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'purchase_id',
        'quantity_in',
        'quantity_remaining',
        'unit_cost',
        'entry_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'integer',
            'quantity_remaining' => 'integer',
            'unit_cost' => 'decimal:2',
            'entry_date' => 'date',
        ];
    }

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Méthodes utiles
    public function isFullyConsumed()
    {
        return $this->quantity_remaining <= 0;
    }

    public function getTotalValue()
    {
        return $this->quantity_remaining * $this->unit_cost;
    }
}