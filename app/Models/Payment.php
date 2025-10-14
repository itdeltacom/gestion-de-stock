<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'sale_id',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_reference',
        'note',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'sale_id', 'payment_date', 'amount', 'payment_method'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $year = date('Y');
                $month = date('m');
                $lastPayment = self::where('reference', 'like', "PAY-{$year}{$month}-%")
                    ->orderBy('reference', 'desc')
                    ->first();

                if ($lastPayment) {
                    $lastNumber = (int) substr($lastPayment->reference, -5);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $payment->reference = "PAY-{$year}{$month}-" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relations
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utiles
    public function getPaymentMethodLabel()
    {
        return match ($this->payment_method) {
            'espece' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'autre' => 'Autre',
            default => $this->payment_method,
        };
    }
}