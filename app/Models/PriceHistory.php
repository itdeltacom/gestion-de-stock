<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PriceHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'purchase_price',
        'sale_price',
        'average_cost',
        'date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'average_cost' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'supplier_id', 'purchase_price', 'sale_price', 'average_cost', 'date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Méthodes utiles
    public function getMargin()
    {
        if ($this->purchase_price == 0) {
            return 0;
        }
        return (($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100;
    }
}