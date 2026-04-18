<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_hours', function (Blueprint $table) {
            if (! Schema::hasColumn('clinic_hours', 'lunch_start_time')) {
                $table->time('lunch_start_time')->nullable()->after('closing_time');
            }

            if (! Schema::hasColumn('clinic_hours', 'lunch_end_time')) {
                $table->time('lunch_end_time')->nullable()->after('lunch_start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinic_hours', function (Blueprint $table) {
            if (Schema::hasColumn('clinic_hours', 'lunch_end_time')) {
                $table->dropColumn('lunch_end_time');
            }

            if (Schema::hasColumn('clinic_hours', 'lunch_start_time')) {
                $table->dropColumn('lunch_start_time');
            }
        });
    }
};