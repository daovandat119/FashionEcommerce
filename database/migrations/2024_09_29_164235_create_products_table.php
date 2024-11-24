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
            $table->foreignId('CategoryID')->constrained('categories', 'CategoryID')->onDelete('cascade');
            $table->string('ProductName');
            $table->string('MainImageURL',255);
            $table->decimal('Price', 10, 2);
            $table->decimal('SalePrice', 10, 2);
            $table->integer('Views')->default(0);
            $table->text('ShortDescription')->nullable();
            $table->text('Description')->nullable();
            $table->enum('Status', ['ACTIVE', 'INACTIVE'])->nullable();
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
