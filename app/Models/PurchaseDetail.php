<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'tva_rate',
        'tva_amount',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'tva_rate' => 'decimal:2',
            'tva_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Relations
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
}