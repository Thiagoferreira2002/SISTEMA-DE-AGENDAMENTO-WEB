<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('cep', 10)->nullable()->after('endereco');
            $table->string('bairro')->nullable()->after('cep');
            $table->string('complemento')->nullable()->after('bairro');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['cep', 'bairro', 'complemento']);
        });
    }
};
