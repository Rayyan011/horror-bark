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
        Schema::create('ferries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Example: "Main Island to Theme Park", "Hotel Island Ferry #1", etc.
            $table->string('route')->nullable();
            $table->unsignedInteger('default_capacity')->default(0);

            // Basic open/close times if you only run ferries during certain hours
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();

            // Optionally store departure/arrival locations explicitly:
            // $table->string('departure_location')->nullable();
            // $table->string('arrival_location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ferries');
    }
};
