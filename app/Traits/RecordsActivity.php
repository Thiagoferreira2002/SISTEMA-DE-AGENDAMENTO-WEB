<?php

namespace App\Traits;

use App\Models\Agendamento;
use App\Models\ActivityLog;
use App\Models\ClinicHour;
use App\Models\Insurance;
use App\Models\InsurancePlan;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\ProcedurePrice;
use App\Models\Professional;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait RecordsActivity
{
    protected function recordActivity(string $action, Model $subject, string $description, array $properties = []): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        $inferredTarget = $this->resolveActivityTarget($subject);

        if (! empty($inferredTarget)) {
            $properties['target_user'] = array_filter(array_merge(
                $inferredTarget,
                is_array($properties['target_user'] ?? null) ? $properties['target_user'] : []
            ), fn ($value) => $value !== null && $value !== '');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    protected function resolveActivityTarget(Model $subject): array
    {
        return match (true) {
            $subject instanceof User => [
                'id' => $subject->getKey(),
                'nome' => trim(($subject->nome ?? '') . ' ' . ($subject->sobrenome ?? '')),
                'cpf' => $subject->cpf,
                'email' => $subject->email,
            ],
            $subject instanceof Patient => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
                'cpf' => $subject->cpf,
                'email' => $subject->email,
            ],
            $subject instanceof Professional => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
                'cpf' => $subject->cpf,
                'email' => optional($subject->user)->email,
            ],
            $subject instanceof Agendamento => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
                'email' => $subject->email,
            ],
            $subject instanceof Procedure => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
            ],
            $subject instanceof Insurance => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
            ],
            $subject instanceof InsurancePlan => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
            ],
            $subject instanceof ProcedurePrice => [
                'id' => $subject->getKey(),
                'nome' => 'Tabela de preço',
            ],
            $subject instanceof Unit => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
            ],
            $subject instanceof Room => [
                'id' => $subject->getKey(),
                'nome' => $subject->nome,
            ],
            $subject instanceof ClinicHour => [
                'id' => $subject->getKey(),
                'nome' => 'Horário da clínica',
            ],
            default => [],
        };
    }
}
