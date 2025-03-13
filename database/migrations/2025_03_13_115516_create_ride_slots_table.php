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
        Schema::create('ride_slots', function (Blueprint $table) {
            $table->id();
            // Link to the ride
            $table->foreignId('ride_id')->constrained()->cascadeOnDelete();

            // The date of this slot
            $table->date('slot_date');

            // Start/end times (for an hour slot, e.g. 09:00 -> 10:00)
            $table->time('start_time');
            $table->time('end_time');

            // Capacity specifically for this slot
            $table->unsignedInteger('capacity')->default(0);

            // e.g., open, closed, maintenance
            $table->string('status')->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_slots');
    }
};
