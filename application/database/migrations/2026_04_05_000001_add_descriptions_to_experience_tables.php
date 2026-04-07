<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->text('description')->nullable()->after('location');
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('beach_events', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('ferries', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('ferries', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('beach_events', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
