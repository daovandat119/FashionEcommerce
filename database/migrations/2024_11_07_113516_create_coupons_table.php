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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('CouponID');
            $table->string('Name');
            $table->string('Code')->unique();
            $table->integer('DiscountPercentage');
            $table->decimal('MinimumOrderValue', 10, 2)->nullable();
            $table->decimal('MaxAmount', 10, 2)->nullable();
            $table->integer('UsageLimit')->nullable();
            $table->timestamp('ExpiresAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
