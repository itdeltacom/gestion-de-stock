<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'product_id',
        'quantity_ordered',
        'quantity_delivered',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'integer',
            'quantity_delivered' => 'integer',
        ];
    }

    // Relations
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Méthodes
    public function isFullyDelivered()
    {
        return $this->quantity_ordered === $this->quantity_delivered;
    }

    public function getRemainingQuantity()
    {
        return $this->quantity_ordered - $this->quantity_delivered;
    }
}