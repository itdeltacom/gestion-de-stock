<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Stock extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'average_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'average_cost' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'warehouse_id', 'quantity', 'average_cost'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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

    // Méthodes utiles
    public function addStock($quantity, $cost)
    {
        // Calcul CMUP
        $totalValue = ($this->quantity * $this->average_cost) + ($quantity * $cost);
        $this->quantity += $quantity;

        if ($this->quantity > 0) {
            $this->average_cost = $totalValue / $this->quantity;
        }

        $this->save();
    }

    public function reduceStock($quantity)
    {
        if ($this->quantity >= $quantity) {
            $this->quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function getTotalValue()
    {
        return $this->quantity * $this->average_cost;
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->product->alert_stock;
    }
}