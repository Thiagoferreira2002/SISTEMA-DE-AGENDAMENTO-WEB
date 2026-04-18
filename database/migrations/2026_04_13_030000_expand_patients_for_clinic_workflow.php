<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('sexo', 20)->nullable()->after('data_nascimento');
            $table->string('telefone_recado', 20)->nullable()->after('telefone');
            $table->string('convenio')->nullable()->after('email');
            $table->string('numero_carteirinha')->nullable()->after('convenio');
            $table->string('tipo_sanguineo', 5)->nullable()->after('sexo');
            $table->text('alergias')->nullable()->after('tipo_sanguineo');
            $table->boolean('usa_medicacao_continua')->default(false)->after('alergias');
            $table->text('medicacao_continua')->nullable()->after('usa_medicacao_continua');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'sexo',
                'telefone_recado',
                'convenio',
                'numero_carteirinha',
                'tipo_sanguineo',
                'alergias',
                'usa_medicacao_continua',
                'medicacao_continua',
            ]);
        });
    }
};
