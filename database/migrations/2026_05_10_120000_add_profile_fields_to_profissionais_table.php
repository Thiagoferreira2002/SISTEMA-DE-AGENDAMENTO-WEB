<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->json('subespecialidades')->nullable()->after('especialidade_principal');
            $table->string('rqe', 50)->nullable()->after('registro_numero');
        });
    }

    public function down(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->dropColumn(['subespecialidades', 'rqe']);
        });
    }
};
