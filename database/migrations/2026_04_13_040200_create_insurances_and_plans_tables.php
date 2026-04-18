<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->index();
            $table->string('ans', 20)->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->boolean('requires_guide')->default(false);
            $table->boolean('requires_authorization')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('insurance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_id')->constrained('insurances')->cascadeOnDelete();
            $table->string('nome');
            $table->string('codigo')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->unique(['insurance_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_plans');
        Schema::dropIfExists('insurances');
    }
};
