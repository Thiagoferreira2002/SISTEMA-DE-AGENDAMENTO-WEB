<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tableMap = [
        'patients' => 'pacientes',
        'professionals' => 'profissionais',
        'professional_schedules' => 'agendas_profissionais',
        'insurances' => 'convenios',
        'insurance_plans' => 'planos_convenio',
        'procedures' => 'procedimentos',
        'procedure_prices' => 'precos_procedimentos',
        'units' => 'unidades',
        'rooms' => 'salas',
        'clinic_hours' => 'horarios_clinica',
        'activity_logs' => 'logs_atividades',
    ];

    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            foreach ($this->tableMap as $from => $to) {
                $this->renameTableIfNeeded($from, $to);
            }
        });
    }

    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            foreach (array_reverse($this->tableMap, true) as $from => $to) {
                $this->renameTableIfNeeded($to, $from);
            }
        });
    }

    private function renameTableIfNeeded(string $from, string $to): void
    {
        if (! Schema::hasTable($from) || Schema::hasTable($to)) {
            return;
        }

        Schema::rename($from, $to);
    }
};
