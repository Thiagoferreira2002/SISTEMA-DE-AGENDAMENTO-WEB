<?php

namespace App\Models;

use App\Models\Concerns\HasLegacyAttributeAliases;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasLegacyAttributeAliases;

    protected $fillable = [
        'user_id',
        'patient_id',
        'paciente_id',
        'procedure_id',
        'procedimento_id',
        'professional_id',
        'profissional_id',
        'nome',
        'email',
        'telefone',
        'servico',
        'medico',
        'duracao_minutos',
        'motivo_consulta',
        'observacao_alerta',
        'prioridade',
        'preferencia_turno',
        'data_limite_espera',
        'data_agendamento',
        'horario',
        'descricao',
        'status',
    ];

    protected $casts = [
        'data_agendamento' => 'datetime',
        'data_limite_espera' => 'date',
        'notification_read_by' => 'array',
    ];

    protected function legacyAttributeAliases(): array
    {
        return [
            'patient_id' => 'paciente_id',
            'procedure_id' => 'procedimento_id',
            'professional_id' => 'profissional_id',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'paciente_id');
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class, 'procedimento_id');
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'profissional_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
