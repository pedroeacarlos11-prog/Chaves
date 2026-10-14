<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprestimo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')
                ->constrained('funcionario');

            $table->foreignId('chave_id')
                ->constrained('chave');

            $table->date('data_retirada')->nullable();
            $table->time('hora_retirada')->nullable();

            $table->date('data_devolucao')->nullable();
            $table->time('hora_devolucao')->nullable();

            $table->string('status', 30)
                ->default('Em andamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprestimo');
    }
};
