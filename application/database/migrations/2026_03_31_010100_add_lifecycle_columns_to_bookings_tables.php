<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'hotel_bookings',
            'ferry_bookings',
            'ride_bookings',
            'game_bookings',
            'beach_event_bookings',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->timestamp('reminder_sent_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'hotel_bookings',
            'ferry_bookings',
            'ride_bookings',
            'game_bookings',
            'beach_event_bookings',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('reminder_sent_at');
            });
        }
    }
};
