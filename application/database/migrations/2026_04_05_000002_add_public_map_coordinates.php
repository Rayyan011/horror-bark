<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['islands', 'hotels', 'rides', 'games', 'beach_events', 'ferries'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $placeAfter = collect(['longitude', 'latitude', 'island_id', 'location', 'name'])
                ->first(fn (string $column) => Schema::hasColumn($tableName, $column));

            Schema::table($tableName, function (Blueprint $table) use ($placeAfter, $tableName) {
                if (! Schema::hasColumn($tableName, 'map_x')) {
                    $column = $table->decimal('map_x', 5, 2)->nullable();

                    if ($placeAfter) {
                        $column->after($placeAfter);
                    }
                }

                if (! Schema::hasColumn($tableName, 'map_y')) {
                    $table->decimal('map_y', 5, 2)->nullable()->after('map_x');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['ferries', 'beach_events', 'games', 'rides', 'hotels', 'islands'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $columns = collect(['map_x', 'map_y'])
                ->filter(fn (string $column) => Schema::hasColumn($tableName, $column))
                ->values()
                ->all();

            if ($columns === []) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
