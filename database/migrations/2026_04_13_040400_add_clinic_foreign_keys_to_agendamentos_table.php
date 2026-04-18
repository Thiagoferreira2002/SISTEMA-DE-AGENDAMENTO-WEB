<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('procedure_id')->nullable()->after('patient_id')->constrained('procedures')->nullOnDelete();
            $table->foreignId('professional_id')->nullable()->after('procedure_id')->constrained('professionals')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('professional_id')->constrained('units')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->after('unit_id')->constrained('rooms')->nullOnDelete();
            $table->foreignId('insurance_id')->nullable()->after('room_id')->constrained('insurances')->nullOnDelete();
            $table->foreignId('insurance_plan_id')->nullable()->after('insurance_id')->constrained('insurance_plans')->nullOnDelete();
            $table->string('numero_guia')->nullable()->after('insurance_plan_id');
            $table->string('numero_autorizacao')->nullable()->after('numero_guia');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('procedure_id');
            $table->dropConstrainedForeignId('professional_id');
            $table->dropConstrainedForeignId('unit_id');
            $table->dropConstrainedForeignId('room_id');
            $table->dropConstrainedForeignId('insurance_id');
            $table->dropConstrainedForeignId('insurance_plan_id');
            $table->dropColumn(['numero_guia', 'numero_autorizacao']);
        });
    }
};
