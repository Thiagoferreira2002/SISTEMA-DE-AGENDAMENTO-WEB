<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('agendamentos')) {
            Schema::table('agendamentos', function (Blueprint $table) {
                foreach (['unidade_id', 'sala_id', 'convenio_id', 'plano_convenio_id'] as $column) {
                    if (Schema::hasColumn('agendamentos', $column)) {
                        $table->dropForeign([$column]);
                    }
                }
            });

            $columnsToDrop = array_keys(array_filter([
                'unidade_id' => Schema::hasColumn('agendamentos', 'unidade_id'),
                'sala_id' => Schema::hasColumn('agendamentos', 'sala_id'),
                'convenio_id' => Schema::hasColumn('agendamentos', 'convenio_id'),
                'plano_convenio_id' => Schema::hasColumn('agendamentos', 'plano_convenio_id'),
                'unidade' => Schema::hasColumn('agendamentos', 'unidade'),
                'convenio' => Schema::hasColumn('agendamentos', 'convenio'),
                'numero_guia' => Schema::hasColumn('agendamentos', 'numero_guia'),
                'numero_autorizacao' => Schema::hasColumn('agendamentos', 'numero_autorizacao'),
            ]));

            if ($columnsToDrop !== []) {
                Schema::table('agendamentos', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
        }

        Schema::dropIfExists('precos_procedimentos');
        Schema::dropIfExists('planos_convenio');
        Schema::dropIfExists('salas');
        Schema::dropIfExists('convenios');
        Schema::dropIfExists('unidades');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable('unidades')) {
            Schema::create('unidades', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->index();
                $table->string('endereco')->nullable();
                $table->string('telefone', 20)->nullable();
                $table->string('email')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('convenios')) {
            Schema::create('convenios', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->index();
                $table->string('ans', 20)->nullable();
                $table->string('cnpj', 20)->nullable();
                $table->boolean('requires_guide')->default(false);
                $table->boolean('requires_authorization')->default(false);
                $table->boolean('ativo')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('salas')) {
            Schema::create('salas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
                $table->string('nome');
                $table->boolean('ativo')->default(true);
                $table->timestamps();
                $table->unique(['unidade_id', 'nome']);
            });
        }

        if (! Schema::hasTable('planos_convenio')) {
            Schema::create('planos_convenio', function (Blueprint $table) {
                $table->id();
                $table->foreignId('convenio_id')->constrained('convenios')->cascadeOnDelete();
                $table->string('nome');
                $table->string('codigo')->nullable();
                $table->boolean('ativo')->default(true);
                $table->timestamps();
                $table->unique(['convenio_id', 'nome']);
            });
        }

        if (! Schema::hasTable('precos_procedimentos')) {
            Schema::create('precos_procedimentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('procedimento_id')->constrained('procedimentos')->cascadeOnDelete();
                $table->foreignId('convenio_id')->constrained('convenios')->cascadeOnDelete();
                $table->foreignId('plano_convenio_id')->nullable()->constrained('planos_convenio')->nullOnDelete();
                $table->decimal('valor', 10, 2);
                $table->timestamps();
                $table->unique(['procedimento_id', 'convenio_id', 'plano_convenio_id'], 'preco_procedimento_unique');
            });
        }

        if (Schema::hasTable('agendamentos')) {
            Schema::table('agendamentos', function (Blueprint $table) {
                if (! Schema::hasColumn('agendamentos', 'unidade_id')) {
                    $table->foreignId('unidade_id')->nullable()->after('profissional_id')->constrained('unidades')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendamentos', 'sala_id')) {
                    $table->foreignId('sala_id')->nullable()->after('unidade_id')->constrained('salas')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendamentos', 'convenio_id')) {
                    $table->foreignId('convenio_id')->nullable()->after('sala_id')->constrained('convenios')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendamentos', 'plano_convenio_id')) {
                    $table->foreignId('plano_convenio_id')->nullable()->after('convenio_id')->constrained('planos_convenio')->nullOnDelete();
                }

                if (! Schema::hasColumn('agendamentos', 'numero_guia')) {
                    $table->string('numero_guia')->nullable()->after('plano_convenio_id');
                }

                if (! Schema::hasColumn('agendamentos', 'numero_autorizacao')) {
                    $table->string('numero_autorizacao')->nullable()->after('numero_guia');
                }

                if (! Schema::hasColumn('agendamentos', 'unidade')) {
                    $table->string('unidade')->nullable()->after('medico');
                }

                if (! Schema::hasColumn('agendamentos', 'convenio')) {
                    $table->string('convenio')->nullable()->after('unidade');
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
