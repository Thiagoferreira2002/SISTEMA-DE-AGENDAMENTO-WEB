<?php

namespace App\Models;

use App\Models\Concerns\HasLegacyAttributeAliases;
use Illuminate\Database\Eloquent\Model;

class ProfessionalAbsence extends Model
{
    use HasLegacyAttributeAliases;

    protected $table = 'ausencias_profissionais';

    protected $fillable = [
        'professional_id',
        'profissional_id',
        'data_ausencia',
        'hora_inicial',
        'hora_final',
        'motivo',
        'observacao',
    ];

    protected $casts = [
        'data_ausencia' => 'date',
    ];

    protected function legacyAttributeAliases(): array
    {
        return [
            'professional_id' => 'profissional_id',
        ];
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'profissional_id');
    }
}
