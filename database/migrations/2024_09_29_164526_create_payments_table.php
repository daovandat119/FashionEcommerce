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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->foreignId('OrderID')->constrained('orders', 'OrderID');
            $table->foreignId('PaymentMethodID')->constrained('payment_methods', 'PaymentMethodID');
            $table->foreignId('PaymentStatusID')->constrained('payment_statuses', 'PaymentStatusID');
            $table->decimal('Amount', 10, 2);
            $table->string('TransactionID')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
