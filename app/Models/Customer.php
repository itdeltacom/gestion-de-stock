<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
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
        'credit_limit',
        'current_credit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'current_credit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'type', 'ice', 'raison_sociale', 'email', 'phone', 'credit_limit', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->code)) {
                $lastCustomer = self::orderBy('code', 'desc')->first();

                if ($lastCustomer) {
                    $lastNumber = (int) substr($lastCustomer->code, 3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $customer->code = 'CLI' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Méthodes utiles
    public function canBuyOnCredit($amount)
    {
        $availableCredit = $this->credit_limit - $this->current_credit;
        return $availableCredit >= $amount;
    }

    public function getRemainingCredit()
    {
        return $this->credit_limit - $this->current_credit;
    }

    public function addCredit($amount)
    {
        $this->current_credit += $amount;
        $this->save();
    }

    public function reduceCredit($amount)
    {
        $this->current_credit = max(0, $this->current_credit - $amount);
        $this->save();
    }

    public function getTotalSales()
    {
        return $this->sales()->where('status', 'valide')->sum('total_ttc');
    }

    public function getDisplayName()
    {
        return $this->type === 'societe' && !empty($this->raison_sociale)
            ? $this->raison_sociale
            : $this->name;
    }
}