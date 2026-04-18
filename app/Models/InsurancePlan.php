<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    protected $fillable = [
        'insurance_id',
        'nome',
        'codigo',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function procedurePrices()
    {
        return $this->hasMany(ProcedurePrice::class, 'insurance_plan_id');
    }
}
