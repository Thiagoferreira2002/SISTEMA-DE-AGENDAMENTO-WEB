<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome')->index();
            $table->string('especialidade_principal');
            $table->string('cpf', 20)->nullable()->unique();
            $table->string('registro_tipo', 20)->default('CRM');
            $table->string('registro_numero', 50);
            $table->string('agenda_color', 20)->default('#0d6efd');
            $table->decimal('repasse_percentual', 5, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('professional_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unique(['professional_id', 'day_of_week', 'start_time', 'end_time'], 'professional_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_schedules');
        Schema::dropIfExists('professionals');
    }
};
