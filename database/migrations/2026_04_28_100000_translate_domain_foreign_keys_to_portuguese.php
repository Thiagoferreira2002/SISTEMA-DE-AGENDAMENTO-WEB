<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('agendamentos') && Schema::hasColumn('agendamentos', 'patient_id')) {
            Schema::table('agendamentos', function (Blueprint $table) {
                $table->dropForeign(['patient_id']);
                $table->dropForeign(['procedure_id']);
                $table->dropForeign(['professional_id']);
                $table->dropForeign(['unit_id']);
                $table->dropForeign(['room_id']);
                $table->dropForeign(['insurance_id']);
                $table->dropForeign(['insurance_plan_id']);
            });

            Schema::table('agendamentos', function (Blueprint $table) {
                $table->renameColumn('patient_id', 'paciente_id');
                $table->renameColumn('procedure_id', 'procedimento_id');
                $table->renameColumn('professional_id', 'profissional_id');
                $table->renameColumn('unit_id', 'unidade_id');
                $table->renameColumn('room_id', 'sala_id');
                $table->renameColumn('insurance_id', 'convenio_id');
                $table->renameColumn('insurance_plan_id', 'plano_convenio_id');
            });

            Schema::table('agendamentos', function (Blueprint $table) {
                $table->foreign('paciente_id', 'agendamentos_paciente_id_foreign')->references('id')->on('pacientes')->nullOnDelete();
                $table->foreign('procedimento_id', 'agendamentos_procedimento_id_foreign')->references('id')->on('procedimentos')->nullOnDelete();
                $table->foreign('profissional_id', 'agendamentos_profissional_id_foreign')->references('id')->on('profissionais')->nullOnDelete();
                $table->foreign('unidade_id', 'agendamentos_unidade_id_foreign')->references('id')->on('unidades')->nullOnDelete();
                $table->foreign('sala_id', 'agendamentos_sala_id_foreign')->references('id')->on('salas')->nullOnDelete();
                $table->foreign('convenio_id', 'agendamentos_convenio_id_foreign')->references('id')->on('convenios')->nullOnDelete();
                $table->foreign('plano_convenio_id', 'agendamentos_plano_convenio_id_foreign')->references('id')->on('planos_convenio')->nullOnDelete();
            });
        }

        if (Schema::hasTable('agendas_profissionais') && Schema::hasColumn('agendas_profissionais', 'professional_id')) {
            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->dropForeign('professional_schedules_professional_id_foreign');
                $table->dropUnique('professional_schedule_unique');
            });

            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->renameColumn('professional_id', 'profissional_id');
            });

            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->foreign('profissional_id', 'agendas_profissionais_profissional_id_foreign')->references('id')->on('profissionais')->cascadeOnDelete();
                $table->unique(['profissional_id', 'day_of_week', 'start_time', 'end_time'], 'agenda_profissional_unica');
            });
        }

        if (Schema::hasTable('planos_convenio') && Schema::hasColumn('planos_convenio', 'insurance_id')) {
            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->dropForeign('insurance_plans_insurance_id_foreign');
                $table->dropUnique('insurance_plans_insurance_id_nome_unique');
            });

            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->renameColumn('insurance_id', 'convenio_id');
            });

            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->foreign('convenio_id', 'planos_convenio_convenio_id_foreign')->references('id')->on('convenios')->cascadeOnDelete();
                $table->unique(['convenio_id', 'nome'], 'planos_convenio_convenio_id_nome_unique');
            });
        }

        if (Schema::hasTable('procedimentos') && Schema::hasColumn('procedimentos', 'professional_id')) {
            Schema::table('procedimentos', function (Blueprint $table) {
                $table->dropForeign('procedures_professional_id_foreign');
            });

            Schema::table('procedimentos', function (Blueprint $table) {
                $table->renameColumn('professional_id', 'profissional_id');
            });

            Schema::table('procedimentos', function (Blueprint $table) {
                $table->foreign('profissional_id', 'procedimentos_profissional_id_foreign')->references('id')->on('profissionais')->nullOnDelete();
            });
        }

        if (Schema::hasTable('precos_procedimentos') && Schema::hasColumn('precos_procedimentos', 'procedure_id')) {
            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->dropForeign('procedure_prices_procedure_id_foreign');
                $table->dropForeign('procedure_prices_insurance_id_foreign');
                $table->dropForeign('procedure_prices_insurance_plan_id_foreign');
                $table->dropUnique('procedure_price_unique');
            });

            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->renameColumn('procedure_id', 'procedimento_id');
                $table->renameColumn('insurance_id', 'convenio_id');
                $table->renameColumn('insurance_plan_id', 'plano_convenio_id');
            });

            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->foreign('procedimento_id', 'precos_procedimentos_procedimento_id_foreign')->references('id')->on('procedimentos')->cascadeOnDelete();
                $table->foreign('convenio_id', 'precos_procedimentos_convenio_id_foreign')->references('id')->on('convenios')->cascadeOnDelete();
                $table->foreign('plano_convenio_id', 'precos_procedimentos_plano_convenio_id_foreign')->references('id')->on('planos_convenio')->nullOnDelete();
                $table->unique(['procedimento_id', 'convenio_id', 'plano_convenio_id'], 'preco_procedimento_unico');
            });
        }

        if (Schema::hasTable('salas') && Schema::hasColumn('salas', 'unit_id')) {
            Schema::table('salas', function (Blueprint $table) {
                $table->dropForeign('rooms_unit_id_foreign');
                $table->dropUnique('rooms_unit_id_nome_unique');
            });

            Schema::table('salas', function (Blueprint $table) {
                $table->renameColumn('unit_id', 'unidade_id');
            });

            Schema::table('salas', function (Blueprint $table) {
                $table->foreign('unidade_id', 'salas_unidade_id_foreign')->references('id')->on('unidades')->cascadeOnDelete();
                $table->unique(['unidade_id', 'nome'], 'salas_unidade_id_nome_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('salas') && Schema::hasColumn('salas', 'unidade_id')) {
            Schema::table('salas', function (Blueprint $table) {
                $table->dropForeign('salas_unidade_id_foreign');
                $table->dropUnique('salas_unidade_id_nome_unique');
            });

            Schema::table('salas', function (Blueprint $table) {
                $table->renameColumn('unidade_id', 'unit_id');
            });

            Schema::table('salas', function (Blueprint $table) {
                $table->foreign('unit_id', 'rooms_unit_id_foreign')->references('id')->on('unidades')->cascadeOnDelete();
                $table->unique(['unit_id', 'nome'], 'rooms_unit_id_nome_unique');
            });
        }

        if (Schema::hasTable('precos_procedimentos') && Schema::hasColumn('precos_procedimentos', 'procedimento_id')) {
            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->dropForeign('precos_procedimentos_procedimento_id_foreign');
                $table->dropForeign('precos_procedimentos_convenio_id_foreign');
                $table->dropForeign('precos_procedimentos_plano_convenio_id_foreign');
                $table->dropUnique('preco_procedimento_unico');
            });

            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->renameColumn('procedimento_id', 'procedure_id');
                $table->renameColumn('convenio_id', 'insurance_id');
                $table->renameColumn('plano_convenio_id', 'insurance_plan_id');
            });

            Schema::table('precos_procedimentos', function (Blueprint $table) {
                $table->foreign('procedure_id', 'procedure_prices_procedure_id_foreign')->references('id')->on('procedimentos')->cascadeOnDelete();
                $table->foreign('insurance_id', 'procedure_prices_insurance_id_foreign')->references('id')->on('convenios')->cascadeOnDelete();
                $table->foreign('insurance_plan_id', 'procedure_prices_insurance_plan_id_foreign')->references('id')->on('planos_convenio')->nullOnDelete();
                $table->unique(['procedure_id', 'insurance_id', 'insurance_plan_id'], 'procedure_price_unique');
            });
        }

        if (Schema::hasTable('procedimentos') && Schema::hasColumn('procedimentos', 'profissional_id')) {
            Schema::table('procedimentos', function (Blueprint $table) {
                $table->dropForeign('procedimentos_profissional_id_foreign');
            });

            Schema::table('procedimentos', function (Blueprint $table) {
                $table->renameColumn('profissional_id', 'professional_id');
            });

            Schema::table('procedimentos', function (Blueprint $table) {
                $table->foreign('professional_id', 'procedures_professional_id_foreign')->references('id')->on('profissionais')->nullOnDelete();
            });
        }

        if (Schema::hasTable('planos_convenio') && Schema::hasColumn('planos_convenio', 'convenio_id')) {
            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->dropForeign('planos_convenio_convenio_id_foreign');
                $table->dropUnique('planos_convenio_convenio_id_nome_unique');
            });

            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->renameColumn('convenio_id', 'insurance_id');
            });

            Schema::table('planos_convenio', function (Blueprint $table) {
                $table->foreign('insurance_id', 'insurance_plans_insurance_id_foreign')->references('id')->on('convenios')->cascadeOnDelete();
                $table->unique(['insurance_id', 'nome'], 'insurance_plans_insurance_id_nome_unique');
            });
        }

        if (Schema::hasTable('agendas_profissionais') && Schema::hasColumn('agendas_profissionais', 'profissional_id')) {
            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->dropForeign('agendas_profissionais_profissional_id_foreign');
                $table->dropUnique('agenda_profissional_unica');
            });

            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->renameColumn('profissional_id', 'professional_id');
            });

            Schema::table('agendas_profissionais', function (Blueprint $table) {
                $table->foreign('professional_id', 'professional_schedules_professional_id_foreign')->references('id')->on('profissionais')->cascadeOnDelete();
                $table->unique(['professional_id', 'day_of_week', 'start_time', 'end_time'], 'professional_schedule_unique');
            });
        }

        if (Schema::hasTable('agendamentos') && Schema::hasColumn('agendamentos', 'paciente_id')) {
            Schema::table('agendamentos', function (Blueprint $table) {
                $table->dropForeign('agendamentos_paciente_id_foreign');
                $table->dropForeign('agendamentos_procedimento_id_foreign');
                $table->dropForeign('agendamentos_profissional_id_foreign');
                $table->dropForeign('agendamentos_unidade_id_foreign');
                $table->dropForeign('agendamentos_sala_id_foreign');
                $table->dropForeign('agendamentos_convenio_id_foreign');
                $table->dropForeign('agendamentos_plano_convenio_id_foreign');
            });

            Schema::table('agendamentos', function (Blueprint $table) {
                $table->renameColumn('paciente_id', 'patient_id');
                $table->renameColumn('procedimento_id', 'procedure_id');
                $table->renameColumn('profissional_id', 'professional_id');
                $table->renameColumn('unidade_id', 'unit_id');
                $table->renameColumn('sala_id', 'room_id');
                $table->renameColumn('convenio_id', 'insurance_id');
                $table->renameColumn('plano_convenio_id', 'insurance_plan_id');
            });

            Schema::table('agendamentos', function (Blueprint $table) {
                $table->foreign('patient_id', 'agendamentos_patient_id_foreign')->references('id')->on('pacientes')->nullOnDelete();
                $table->foreign('procedure_id', 'agendamentos_procedure_id_foreign')->references('id')->on('procedimentos')->nullOnDelete();
                $table->foreign('professional_id', 'agendamentos_professional_id_foreign')->references('id')->on('profissionais')->nullOnDelete();
                $table->foreign('unit_id', 'agendamentos_unit_id_foreign')->references('id')->on('unidades')->nullOnDelete();
                $table->foreign('room_id', 'agendamentos_room_id_foreign')->references('id')->on('salas')->nullOnDelete();
                $table->foreign('insurance_id', 'agendamentos_insurance_id_foreign')->references('id')->on('convenios')->nullOnDelete();
                $table->foreign('insurance_plan_id', 'agendamentos_insurance_plan_id_foreign')->references('id')->on('planos_convenio')->nullOnDelete();
            });
        }
    }
};
