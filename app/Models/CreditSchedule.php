<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class CreditSchedule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'sale_id',
        'customer_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_amount',
        'status',
        'payment_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'payment_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sale_id', 'customer_id', 'amount', 'due_date', 'status', 'payment_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($schedule) {
            // Vérifier si l'échéance est en retard
            if ($schedule->status === 'en_attente' && Carbon::parse($schedule->due_date)->isPast()) {
                $schedule->status = 'retard';
                $schedule->saveQuietly();
            }
        });
    }

    // Relations
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Méthodes utiles
    public function getRemainingAmount()
    {
        return $this->amount - $this->paid_amount;
    }

    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->amount;
    }

    public function isOverdue()
    {
        return $this->status === 'retard' ||
            ($this->status === 'en_attente' && Carbon::parse($this->due_date)->isPast());
    }

    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return Carbon::parse($this->due_date)->diffInDays(Carbon::now());
    }

    public function recordPayment($amount, $paymentDate = null)
    {
        $this->paid_amount += $amount;

        if ($this->isFullyPaid()) {
            $this->status = 'paye';
            $this->payment_date = $paymentDate ?? now();
        }

        $this->save();
    }
}