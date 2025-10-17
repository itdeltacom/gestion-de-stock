<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'type',
        'customer_id',
        'warehouse_id',
        'user_id',
        'sale_date',
        'total_ht',
        'total_tva',
        'total_ttc',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'status',
        'is_credit',
        'from_pos',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_ht' => 'decimal:2',
            'total_tva' => 'decimal:2',
            'total_ttc' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'is_credit' => 'boolean',
            'from_pos' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['reference', 'type', 'customer_id', 'warehouse_id', 'sale_date', 'total_ttc', 'status', 'payment_status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->reference)) {
                $year = date('Y');
                $month = date('m');

                $prefix = match ($sale->type) {
                    'devis' => 'DEV',
                    'bon_commande' => 'BC',
                    'facture' => 'FAC',
                    default => 'FAC',
                };

                $lastSale = self::where('reference', 'like', "{$prefix}-{$year}{$month}-%")
                    ->orderBy('reference', 'desc')
                    ->first();

                if ($lastSale) {
                    $lastNumber = (int) substr($lastSale->reference, -5);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $sale->reference = "{$prefix}-{$year}{$month}-" . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            }
        });

        // Mettre à jour le stock et le crédit client quand la vente est validée
        static::updated(function ($sale) {
            if ($sale->isDirty('status') && $sale->status === 'valide') {
                $sale->updateStock();
                $sale->updateCustomerCredit();
            }
        });
    }

    // Relations
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
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function creditSchedules()
    {
        return $this->hasMany(CreditSchedule::class)->orderBy('installment_number');
    }
    public function deliveryNote()
    {
        return $this->hasOne(DeliveryNote::class);
    }


    public function hasDeliveryNote()
    {
        return $this->deliveryNote()->exists();
    }

    public function canGenerateDeliveryNote()
    {
        return $this->status === 'valide' && !$this->hasDeliveryNote();
    }
    public function createCreditSchedules($numberOfInstallments, $firstDueDate)
    {
        if (!$this->is_credit) {
            throw new \Exception("Cette vente n'est pas à crédit");
        }

        // Supprimer les anciennes échéances si elles existent
        $this->creditSchedules()->delete();

        $amountPerInstallment = $this->remaining_amount / $numberOfInstallments;
        $dueDate = Carbon::parse($firstDueDate);

        for ($i = 1; $i <= $numberOfInstallments; $i++) {
            CreditSchedule::create([
                'sale_id' => $this->id,
                'customer_id' => $this->customer_id,
                'installment_number' => $i,
                'amount' => $amountPerInstallment,
                'due_date' => $dueDate->copy(),
                'status' => 'en_attente',
            ]);

            // Ajouter 30 jours pour la prochaine échéance
            $dueDate->addDays(30);
        }
    }

    // Modifier la méthode addPayment pour gérer les échéances
    public function addPayment($amount, $method, $reference = null, $note = null)
    {
        $payment = Payment::create([
            'sale_id' => $this->id,
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method' => $method,
            'transaction_reference' => $reference,
            'note' => $note,
            'user_id' => auth()->id(),
        ]);

        $this->paid_amount += $amount;
        $this->remaining_amount = $this->total_ttc - $this->paid_amount;

        // Distribuer le paiement sur les échéances si vente à crédit
        if ($this->is_credit) {
            $this->distributePaymentToSchedules($amount);
            $this->customer->reduceCredit($amount);
        }

        // Mettre à jour le statut de paiement
        if ($this->paid_amount >= $this->total_ttc) {
            $this->payment_status = 'paye';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partiel';
        }

        $this->save();

        return $payment;
    }

    //  méthode pour distribuer les paiements sur les échéances
    private function distributePaymentToSchedules($amount)
    {
        $remainingAmount = $amount;

        // Récupérer les échéances non payées, triées par date d'échéance
        $schedules = $this->creditSchedules()
            ->where('status', '!=', 'paye')
            ->orderBy('due_date', 'asc')
            ->get();

        foreach ($schedules as $schedule) {
            if ($remainingAmount <= 0) {
                break;
            }

            $scheduleRemaining = $schedule->getRemainingAmount();
            $paymentForSchedule = min($remainingAmount, $scheduleRemaining);

            $schedule->recordPayment($paymentForSchedule, now());
            $remainingAmount -= $paymentForSchedule;
        }
    }

    // Méthode pour obtenir les échéances en retard
    public function getOverdueSchedules()
    {
        return $this->creditSchedules()
            ->where('status', 'retard')
            ->orWhere(function ($query) {
                $query->where('status', 'en_attente')
                    ->where('due_date', '<', now());
            })
            ->get();
    }
    // Méthodes utiles
    public function calculateTotals()
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($this->details as $detail) {
            $totalHt += $detail->unit_price * $detail->quantity;
            $totalTva += $detail->tva_amount;
        }

        $this->total_ht = $totalHt;
        $this->total_tva = $totalTva;
        $this->total_ttc = $totalHt + $totalTva;
        $this->remaining_amount = $this->total_ttc - $this->paid_amount;

        // Mettre à jour le statut de paiement
        if ($this->paid_amount == 0) {
            $this->payment_status = 'non_paye';
        } elseif ($this->paid_amount >= $this->total_ttc) {
            $this->payment_status = 'paye';
        } else {
            $this->payment_status = 'partiel';
        }

        $this->save();
    }

    public function updateStock()
    {
        foreach ($this->details as $detail) {
            $product = $detail->product;
            $stock = Stock::where('product_id', $detail->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            if (!$stock || $stock->quantity < $detail->quantity) {
                throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
            }

            if ($product->stock_method === 'cmup') {
                // Réduire le stock simplement
                $stock->reduceStock($detail->quantity);
            } else {
                // Méthode FIFO - réduire en suivant l'ordre d'entrée
                $this->reduceFifoStock($detail->product_id, $detail->quantity);
                $stock->quantity -= $detail->quantity;
                $stock->save();
            }
        }
    }

    private function reduceFifoStock($productId, $quantity)
    {
        $remainingQuantity = $quantity;

        $fifoEntries = StockFifo::where('product_id', $productId)
            ->where('warehouse_id', $this->warehouse_id)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('entry_date', 'asc')
            ->get();

        foreach ($fifoEntries as $entry) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $quantityToReduce = min($remainingQuantity, $entry->quantity_remaining);
            $entry->quantity_remaining -= $quantityToReduce;
            $entry->save();

            $remainingQuantity -= $quantityToReduce;
        }

        if ($remainingQuantity > 0) {
            throw new \Exception("Stock FIFO insuffisant");
        }
    }

    public function updateCustomerCredit()
    {
        if ($this->is_credit) {
            $this->customer->addCredit($this->remaining_amount);
        }
    }


    public function canBeDeleted()
    {
        return $this->status === 'en_attente';
    }

    public function canBeConverted()
    {
        return in_array($this->type, ['devis', 'bon_commande']) && $this->status === 'valide';
    }

    public function convertToInvoice()
    {
        if (!$this->canBeConverted()) {
            throw new \Exception("Cette vente ne peut pas être convertie en facture");
        }

        $invoice = self::create([
            'type' => 'facture',
            'customer_id' => $this->customer_id,
            'warehouse_id' => $this->warehouse_id,
            'user_id' => auth()->id(),
            'sale_date' => now(),
            'total_ht' => $this->total_ht,
            'total_tva' => $this->total_tva,
            'total_ttc' => $this->total_ttc,
            'is_credit' => $this->is_credit,
            'note' => "Converti depuis {$this->reference}",
        ]);

        // Copier les détails
        foreach ($this->details as $detail) {
            SaleDetail::create([
                'sale_id' => $invoice->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'cost_price' => $detail->cost_price,
                'tva_rate' => $detail->tva_rate,
                'tva_amount' => $detail->tva_amount,
                'total' => $detail->total,
            ]);
        }

        return $invoice;
    }
}