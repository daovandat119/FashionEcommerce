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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('AddressID');
            $table->foreignId('UserID')->constrained('users', 'UserID');
            $table->string('Username',255);
            $table->string('Address');
            $table->string('PhoneNumber', 20);
            $table->string('ProvinceID', 20);
            $table->string('DistrictID', 20);
            $table->string('WardCode', 20);
            $table->boolean('IsDefault')->default(0);
            $table->enum('Status', ['ACTIVE', 'INACTIVE'])->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
