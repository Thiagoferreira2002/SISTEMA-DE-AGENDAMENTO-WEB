<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dados extends Model
{

    protected $table = 'dados';
    protected $fillable = [

        'logo',
        'icone',
        'nome',
        'cnpj',
        'descricao',
        'email',
        'fone',
        'whatssapp',
        'endereco',
        'numero',
        'cep',
        'estado',
        'cidade'
    ];
}
