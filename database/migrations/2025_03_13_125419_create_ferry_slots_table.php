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

            // The specific ferry
            $table->foreignId('ferry_id')->constrained()->cascadeOnDelete();

            // The date for this slot
            $table->date('slot_date');

            // Start/end times for an hour-long ferry window or departure time
            $table->time('start_time');
            $table->time('end_time');

            // Capacity for this particular slot
            $table->unsignedInteger('capacity')->default(0);

            // e.g. open, closed, canceled
            $table->string('status')->default('open');

            $table->timestamps();
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
