<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['islands', 'hotels', 'rides', 'games', 'beach_events', 'ferries'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('map_x', 5, 2)->nullable()->after('longitude');
                $table->decimal('map_y', 5, 2)->nullable()->after('map_x');
            });
        }
    }

    public function down(): void
    {
        foreach (['ferries', 'beach_events', 'games', 'rides', 'hotels', 'islands'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['map_x', 'map_y']);
            });
        }
    }
};
