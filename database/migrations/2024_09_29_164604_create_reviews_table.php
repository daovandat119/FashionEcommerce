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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('ReviewID');
            $table->foreignId('UserID')->constrained('users', 'UserID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID');
            $table->foreignId('RatingLevelID')->nullable()->constrained('rating_levels', 'RatingLevelID');
            $table->foreignId('ParentReviewID')->nullable()->constrained('reviews', 'ReviewID');
            $table->text('ReviewContent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
