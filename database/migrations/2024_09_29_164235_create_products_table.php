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
        Schema::create('products', function (Blueprint $table) {
            $table->id('ProductID');
            $table->foreignId('CategoryID')->constrained('categories', 'CategoryID');
            $table->string('ProductName');
            $table->string('MainImageURL',255);
            $table->decimal('Price', 10, 2);
            $table->decimal('SalePrice', 10, 2);
            $table->integer('Views')->default(0);
            $table->string('ShortDescription')->nullable();
            $table->string('Description')->nullable();
            $table->string('Status');
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
