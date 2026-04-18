<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->index();
            $table->unsignedInteger('duracao_minutos');
            $table->string('codigo_tuss', 30)->nullable();
            $table->decimal('valor_particular', 10, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('procedure_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();
            $table->foreignId('insurance_id')->constrained('insurances')->cascadeOnDelete();
            $table->foreignId('insurance_plan_id')->nullable()->constrained('insurance_plans')->nullOnDelete();
            $table->decimal('valor', 10, 2);
            $table->timestamps();
            $table->unique(['procedure_id', 'insurance_id', 'insurance_plan_id'], 'procedure_price_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_prices');
        Schema::dropIfExists('procedures');
    }
};
