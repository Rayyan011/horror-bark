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
        Schema::create('ferry_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ferry_id');
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('ferry_id')->references('id')->on('ferries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ferry_slots');
    }
};
