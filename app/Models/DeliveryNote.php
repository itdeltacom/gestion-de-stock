<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeliveryNote extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'sale_id',
        'customer_id',
        'warehouse_id',
        'user_id',
        'delivery_date',
        'status',
        'delivery_address',
        'contact_person',
        'contact_phone',
        'notes',
        'driver_name',
        'vehicle',
        'delivered_at',
        'recipient_name',
        'recipient_signature',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'delivered_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'delivery_date', 'delivered_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deliveryNote) {
            if (empty($deliveryNote->reference)) {
                $date = now()->format('Ymd');
                $lastBL = self::whereDate('created_at', today())->orderBy('reference', 'desc')->first();

                if ($lastBL) {
                    $lastNumber = (int) substr($lastBL->reference, -4);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $deliveryNote->reference = 'BL' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(DeliveryNoteDetail::class);
    }

    // Méthodes
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'en_attente' => '<span class="badge bg-warning">En Attente</span>',
            'en_cours' => '<span class="badge bg-info">En Cours</span>',
            'livre' => '<span class="badge bg-success">Livré</span>',
            'annule' => '<span class="badge bg-danger">Annulé</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getTotalQuantityOrdered()
    {
        return $this->details->sum('quantity_ordered');
    }

    public function getTotalQuantityDelivered()
    {
        return $this->details->sum('quantity_delivered');
    }

    public function isFullyDelivered()
    {
        return $this->getTotalQuantityOrdered() === $this->getTotalQuantityDelivered();
    }

    public function markAsDelivered($recipientName, $signature = null)
    {
        $this->update([
            'status' => 'livre',
            'delivered_at' => now(),
            'recipient_name' => $recipientName,
            'recipient_signature' => $signature,
        ]);
    }
}