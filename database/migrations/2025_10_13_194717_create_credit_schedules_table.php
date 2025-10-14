<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_number'); // Numéro de l'échéance
            $table->decimal('amount', 15, 2); // Montant de l'échéance
            $table->date('due_date'); // Date d'échéance
            $table->decimal('paid_amount', 15, 2)->default(0); // Montant payé
            $table->enum('status', ['en_attente', 'paye', 'retard'])->default('en_attente');
            $table->date('payment_date')->nullable(); // Date de paiement effectif
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_schedules');
    }
};