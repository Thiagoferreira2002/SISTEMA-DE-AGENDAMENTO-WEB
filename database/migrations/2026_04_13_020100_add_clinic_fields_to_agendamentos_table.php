<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->after('user_id')->constrained('patients')->nullOnDelete();
            $table->string('medico')->nullable()->after('servico');
            $table->string('unidade')->nullable()->after('medico');
            $table->string('convenio')->nullable()->after('unidade');
            $table->unsignedInteger('duracao_minutos')->nullable()->after('convenio');
            $table->text('motivo_consulta')->nullable()->after('duracao_minutos');
            $table->text('observacao_alerta')->nullable()->after('motivo_consulta');
            $table->unsignedTinyInteger('prioridade')->default(0)->after('observacao_alerta');
            $table->string('preferencia_turno')->nullable()->after('prioridade');
            $table->date('data_limite_espera')->nullable()->after('preferencia_turno');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_id');
            $table->dropColumn([
                'medico',
                'unidade',
                'convenio',
                'duracao_minutos',
                'motivo_consulta',
                'observacao_alerta',
                'prioridade',
                'preferencia_turno',
                'data_limite_espera',
            ]);
        });
    }
};
