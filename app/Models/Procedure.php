<?php

namespace App\Models;

use App\Models\Concerns\HasLegacyAttributeAliases;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasLegacyAttributeAliases;

    protected $table = 'procedimentos';

    protected $fillable = [
        'professional_id',
        'profissional_id',
        'nome',
        'duracao_minutos',
        'codigo_tuss',
        'valor_particular',
        'ativo',
    ];

    protected $casts = [
        'valor_particular' => 'decimal:2',
        'ativo' => 'boolean',
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
