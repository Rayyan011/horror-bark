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
        Schema::create('game_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_slot_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_price', 8, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->string('status')->default('booked');
            $table->timestamps();

            $table->foreign('game_slot_id')->references('id')->on('game_slots')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_bookings');
    }
};
