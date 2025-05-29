<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();

            $table->decimal('cost_price', 12, 2)->default(0);    // Harga modal
            $table->decimal('margin', 12, 2)->default(0);        // Margin keuntungan (nominal)
            $table->decimal('tax', 12, 2)->default(0);           // Pajak (nominal)
            $table->decimal('discount', 12, 2)->default(0);      // Diskon (nominal)

            $table->decimal('final_price', 12, 2)->storedAs('cost_price + margin + tax - discount');

            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            
            $table->foreign('category_id')->references('id')->on('category_products');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};