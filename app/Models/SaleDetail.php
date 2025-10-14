<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'cost_price',
        'tva_rate',
        'tva_amount',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'tva_rate' => 'decimal:2',
            'tva_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Relations
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Méthodes utiles
    public function calculateTotals()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $this->tva_amount = $subtotal * ($this->tva_rate / 100);
        $this->total = $subtotal + $this->tva_amount;
    }

    public function getMargin()
    {
        if ($this->cost_price == 0) {
            return 0;
        }
        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getProfitAmount()
    {
        return ($this->unit_price - $this->cost_price) * $this->quantity;
    }
}