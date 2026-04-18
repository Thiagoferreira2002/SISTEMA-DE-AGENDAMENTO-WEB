<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'nome',
        'endereco',
        'telefone',
        'email',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class)->orderBy('nome');
    }
}
