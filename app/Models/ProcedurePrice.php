<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedurePrice extends Model
{
    protected $fillable = [
        'procedure_id',
        'insurance_id',
        'insurance_plan_id',
        'valor',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function plan()
    {
        return $this->belongsTo(InsurancePlan::class, 'insurance_plan_id');
    }
}
