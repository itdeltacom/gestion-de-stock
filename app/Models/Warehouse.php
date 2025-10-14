<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warehouse extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'type', 'address', 'city', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Générer automatiquement le code si non fourni
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($warehouse) {
            if (empty($warehouse->code)) {
                $prefix = $warehouse->type === 'depot' ? 'DEP' : 'PV';
                $lastWarehouse = self::where('code', 'like', $prefix . '%')
                    ->orderBy('code', 'desc')
                    ->first();

                if ($lastWarehouse) {
                    $lastNumber = (int) substr($lastWarehouse->code, strlen($prefix));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $warehouse->code = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }

    // Méthodes utiles
    public function isDepot()
    {
        return $this->type === 'depot';
    }

    public function isPointVente()
    {
        return $this->type === 'point_vente';
    }

    public function getTotalStockValue()
    {
        return $this->stocks()->with('product')->get()->sum(function ($stock) {
            return $stock->quantity * $stock->average_cost;
        });
    }
}