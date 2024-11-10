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
            $table->string('Name'); // Tên của coupon
            $table->string('Code')->unique(); // Mã coupon, phải là duy nhất
            $table->integer('DiscountPercentage'); // Phần trăm giảm giá mà coupon cung cấp
            $table->decimal('MinimumOrderValue', 10, 2)->nullable(); // Giá trị đơn hàng tối thiểu để áp dụng coupon, có thể null
            $table->integer('UsageLimit')->nullable(); // Giới hạn số lần sử dụng coupon, có thể null
            $table->integer('UsedCount')->default(0); // Số lần coupon đã được sử dụng, mặc định là 0
            $table->timestamp('ExpiresAt')->nullable(); // Thời gian hết hạn của coupon, có thể null
            $table->timestamps(); // Timestamps cho created_at và updated_at
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
