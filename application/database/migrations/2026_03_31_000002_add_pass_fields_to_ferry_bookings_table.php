<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ferry_bookings', function (Blueprint $table) {
            $table->string('pass_number')->nullable()->unique()->after('status');
            $table->string('pass_path')->nullable()->after('pass_number');
        });
    }

    public function down(): void
    {
        Schema::table('ferry_bookings', function (Blueprint $table) {
            $table->dropColumn(['pass_number', 'pass_path']);
        });
    }
};
