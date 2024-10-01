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
        Schema::create('users', function (Blueprint $table) {
            $table->id('UserID');
            $table->foreignId('RoleID')->constrained('roles', 'RoleID');
            $table->string('Username');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->string('Image',255)->nullable();
            $table->boolean('IsActive')->default(true);
            $table->string('CodeId', 100)->nullable();
            $table->datetime('CodeExpired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
