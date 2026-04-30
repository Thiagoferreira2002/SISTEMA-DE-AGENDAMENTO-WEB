<?php

namespace App\Models;

use App\Models\Concerns\HasLegacyAttributeAliases;
use Illuminate\Database\Eloquent\Model;

class ProfessionalSchedule extends Model
{
    use HasLegacyAttributeAliases;

    public $timestamps = false;

    protected $table = 'agendas_profissionais';

    protected $fillable = [
        'professional_id',
        'profissional_id',
        'day_of_week',
        'start_time',
        'break_start_time',
        'break_end_time',
        'end_time',
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
