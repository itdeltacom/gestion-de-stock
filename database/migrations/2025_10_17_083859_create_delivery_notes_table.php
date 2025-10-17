<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Créé par
            $table->date('delivery_date');
            $table->enum('status', ['en_attente', 'en_cours', 'livre', 'annule'])->default('en_attente');
            $table->string('delivery_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('vehicle')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('recipient_name')->nullable(); // Nom du réceptionnaire
            $table->string('recipient_signature')->nullable(); // Chemin vers signature numérique
            $table->timestamps();
        });

        Schema::create('delivery_note_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_ordered');
            $table->integer('quantity_delivered');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_details');
        Schema::dropIfExists('delivery_notes');
    }
};