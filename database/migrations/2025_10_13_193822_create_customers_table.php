<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['individuel', 'societe'])->default('individuel');
            $table->string('ice')->nullable(); // Pour les sociétés
            $table->string('raison_sociale')->nullable(); // Pour les sociétés
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0); // Limite de crédit
            $table->decimal('current_credit', 15, 2)->default(0); // Crédit actuel utilisé
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};