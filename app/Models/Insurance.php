<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = [
        'nome',
        'ans',
        'cnpj',
        'requires_guide',
        'requires_authorization',
        'ativo',
    ];

    protected $casts = [
        'requires_guide' => 'boolean',
        'requires_authorization' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function plans()
    {
        return $this->hasMany(InsurancePlan::class)->orderBy('nome');
    }

    public function procedurePrices()
    {
        return $this->hasMany(ProcedurePrice::class);
    }
}
