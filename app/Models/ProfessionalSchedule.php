<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'professional_id',
        'day_of_week',
        'start_time',
        'break_start_time',
        'break_end_time',
        'end_time',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
