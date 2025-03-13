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
        Schema::create('ferry_bookings', function (Blueprint $table) {
            $table->id();

            // Link to user who books the ferry
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Reference the ferry slot
            $table->foreignId('ferry_slot_id')->constrained()->cascadeOnDelete();

            // Price or cost if you charge for ferry
            $table->decimal('total_price', 8, 2)->nullable();

            // e.g. pending, confirmed, canceled, completed
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ferry_bookings');
    }
};
