<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ausencias_profissionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('profissionais')->cascadeOnDelete();
            $table->date('data_ausencia');
            $table->time('hora_inicial');
            $table->time('hora_final');
            $table->string('motivo', 120);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['profissional_id', 'data_ausencia'], 'ausencias_profissionais_data_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ausencias_profissionais');
    }
};
