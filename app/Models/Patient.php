<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Patient extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'nome',
        'foto',
        'cpf',
        'email',
        'telefone',
        'cep',
        'bairro',
        'complemento',
        'numero_endereco',
        'tipo_moradia',
        'telefone_recado',
        'convenio',
        'numero_carteirinha',
        'data_nascimento',
        'sexo',
        'tipo_sanguineo',
        'alergias',
        'usa_medicacao_continua',
        'medicacao_continua',
        'endereco',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'usa_medicacao_continua' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'paciente_id');
    }

    public function getFotoUrlAttribute(): string
    {
        $path = trim((string) $this->foto);

        if ($path === '') {
            return asset('backend/assets/img/avatar/avatar-1.png');
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return $normalizedPath;
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        if (is_file(public_path($normalizedPath))) {
            return asset($normalizedPath);
        }

        if (is_file(public_path('storage/' . $normalizedPath))) {
            return asset('storage/' . $normalizedPath);
        }

        return asset('backend/assets/img/avatar/avatar-1.png');
    }

    public function getCadastroStatusLabelAttribute(): string
    {
        return empty($this->cadastro_pendencias) ? 'Completo' : 'Incompleto';
    }

    public function getCadastroStatusClassAttribute(): string
    {
        return empty($this->cadastro_pendencias) ? 'success' : 'warning';
    }

    public function getCadastroPendenciasAttribute(): array
    {
        $pendencias = [];

        if (! $this->email) {
            $pendencias[] = 'E-mail';
        }

        if (! $this->telefone) {
            $pendencias[] = 'Celular';
        }

        if (! $this->cpf) {
            $pendencias[] = 'CPF';
        }

        if (! $this->data_nascimento) {
            $pendencias[] = 'Data de nascimento';
        }

        if (! $this->sexo) {
            $pendencias[] = 'Sexo';
        }

        if (! $this->endereco) {
            $pendencias[] = 'Endereco';
        }

        if (! $this->numero_endereco) {
            $pendencias[] = 'Numero';
        }

        if (! $this->cep) {
            $pendencias[] = 'CEP';
        }

        if (! $this->bairro) {
            $pendencias[] = 'Bairro';
        }

        if (! $this->tipo_moradia) {
            $pendencias[] = 'Tipo de imovel';
        }

        return $pendencias;
    }
}
