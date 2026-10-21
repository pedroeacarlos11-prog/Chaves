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
            $table->unsignedBigInteger('funcionario_id');
            $table->foreignId('funcionario_id')->references('id')->on('funcionario')->onDelete('cascade');
            $table->unsignedBigInteger('chave_id');
            $table->foreignId('chave_id')->references('id')->on('chave')->onDelete('cascade');

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
        Schema::table('emprestimo', function (Blueprint $table) {
            $table->dropForeign(['funcionario_id']);
            $table->dropColumn(['funcionario_id']);
            $table->dropForeign(['chave_id']);
            $table->dropColumn(['chave_id']);
        });

        Schema::dropIfExists('emprestimo');

    }
};
