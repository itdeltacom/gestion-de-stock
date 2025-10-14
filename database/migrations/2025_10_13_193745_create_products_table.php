<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('reference')->nullable()->unique(); // Référence interne
            $table->string('barcode')->nullable()->unique(); // Code-barres généré
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('tva_rate', 5, 2)->default(20.00);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('current_average_cost', 15, 2)->default(0);
            $table->enum('stock_method', ['cmup', 'fifo'])->default('cmup');
            $table->integer('alert_stock')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};