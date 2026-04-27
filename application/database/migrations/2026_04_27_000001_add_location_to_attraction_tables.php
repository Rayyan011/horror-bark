<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['rides', 'games', 'beach_events', 'ferries'] as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'location')) {
                continue;
            }

            $hasDescription = Schema::hasColumn($tableName, 'description');

            Schema::table($tableName, function (Blueprint $table) use ($hasDescription) {
                $column = $table->string('location')->nullable();

                if ($hasDescription) {
                    $column->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['ferries', 'beach_events', 'games', 'rides'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'location')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }
    }
};
