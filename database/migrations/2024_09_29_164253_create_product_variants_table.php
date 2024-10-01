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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('VariantID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID');
            $table->foreignId('SizeID')->constrained('sizes', 'SizeID');
            $table->foreignId('ColorID')->constrained('colors', 'ColorID');
            $table->integer('Quantity');
            $table->decimal('Price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
