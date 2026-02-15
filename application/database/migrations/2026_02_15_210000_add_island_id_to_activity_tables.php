<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->foreignId('island_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('island_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('beach_events', function (Blueprint $table) {
            $table->foreignId('island_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropConstrainedForeignId('island_id');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('island_id');
        });

        Schema::table('beach_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('island_id');
        });
    }
};
