<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variant_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('VariantID')->constrained('product_variants', 'VariantID')->onDelete('cascade');
            $table->foreignId('ColorID')->constrained('colors', 'ColorID');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variant_colors');
    }
};

