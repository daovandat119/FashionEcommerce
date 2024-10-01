<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('CartItemID');
            $table->foreignId('CartID')->constrained('carts', 'CartID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID');
            $table->foreignId('VariantID')->constrained('product_variants', 'VariantID');
            $table->integer('Quantity');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
