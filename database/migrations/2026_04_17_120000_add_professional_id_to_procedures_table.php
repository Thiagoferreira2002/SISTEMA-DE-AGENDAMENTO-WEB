<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procedures', function (Blueprint $table) {
            if (! Schema::hasColumn('procedures', 'professional_id')) {
                $table->foreignId('professional_id')->nullable()->after('id')->constrained('professionals')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('procedures', function (Blueprint $table) {
            if (Schema::hasColumn('procedures', 'professional_id')) {
                $table->dropConstrainedForeignId('professional_id');
            }
        });
    }
};
