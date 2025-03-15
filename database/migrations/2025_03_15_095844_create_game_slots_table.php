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
        Schema::create('game_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_slots');
    }
};
