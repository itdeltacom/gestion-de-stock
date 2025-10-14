<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'type',
        'ice',
        'raison_sociale',
        'email',
        'phone',
        'address',
        'city',
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
            ->logOnly(['name', 'code', 'type', 'ice', 'raison_sociale', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $lastSupplier = self::orderBy('code', 'desc')->first();
                
                if ($lastSupplier) {
                    $lastNumber = (int) substr($lastSupplier->code, 3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                
                $supplier->code = 'SUP' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    // Méthodes utiles
    public function getTotalPurchases()
    {
        return $this->purchases()->where('status', 'recu')->sum('total_ttc');
    }

    public function getDisplayName()
    {
        return $this->type === 'societe' && !empty($this->raison_sociale) 
            ? $this->raison_sociale 
            : $this->name;
    }
}