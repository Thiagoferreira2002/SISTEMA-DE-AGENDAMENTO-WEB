<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professional extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'especialidade_principal',
        'cpf',
        'registro_tipo',
        'registro_numero',
        'agenda_color',
        'repasse_percentual',
        'ativo',
    ];

    protected $casts = [
        'repasse_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(ProfessionalSchedule::class)->orderBy('day_of_week')->orderBy('start_time');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function getRegistroCompletoAttribute(): string
    {
        return trim(($this->registro_tipo ?: 'CRM') . ' ' . $this->registro_numero);
    }
}
