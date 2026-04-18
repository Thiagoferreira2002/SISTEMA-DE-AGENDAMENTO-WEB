<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $fillable = [
        'professional_id',
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

    public function prices()
    {
        return $this->hasMany(ProcedurePrice::class)->with(['insurance', 'plan']);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
