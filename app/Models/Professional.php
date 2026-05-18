<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $table = 'profissionais';

    protected $fillable = [
        'user_id',
        'nome',
        'especialidade_principal',
        'subespecialidades',
        'cpf',
        'registro_tipo',
        'registro_numero',
        'rqe',
        'agenda_color',
        'repasse_percentual',
        'ativo',
    ];

    protected $casts = [
        'subespecialidades' => 'array',
        'repasse_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(ProfessionalSchedule::class, 'profissional_id')->orderBy('day_of_week')->orderBy('start_time');
    }

    public function absences()
    {
        return $this->hasMany(ProfessionalAbsence::class, 'profissional_id')
            ->orderBy('data_ausencia')
            ->orderBy('hora_inicial');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'profissional_id');
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class, 'profissional_id');
    }

    public function getRegistroCompletoAttribute(): string
    {
        return trim(($this->registro_tipo ?: 'CRM') . ' ' . $this->registro_numero);
    }
}
