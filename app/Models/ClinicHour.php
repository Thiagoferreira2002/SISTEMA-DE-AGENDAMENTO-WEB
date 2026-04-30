<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicHour extends Model
{
    protected $table = 'horarios_clinica';

    protected $fillable = [
        'opening_time',
        'closing_time',
        'lunch_start_time',
        'lunch_end_time',
    ];
}
