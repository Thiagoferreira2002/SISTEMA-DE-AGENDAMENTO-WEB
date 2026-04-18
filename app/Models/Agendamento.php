<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'procedure_id',
        'professional_id',
        'unit_id',
        'room_id',
        'insurance_id',
        'insurance_plan_id',
        'nome',
        'email',
        'telefone',
        'servico',
        'medico',
        'unidade',
        'convenio',
        'numero_guia',
        'numero_autorizacao',
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
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function insurancePlan()
    {
        return $this->belongsTo(InsurancePlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
