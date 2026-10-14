<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chave', function (Blueprint $table) {
            $table->id();
            $table->string('nome_chave', 100);
            $table->string('localizacao', 100)->nullable();
            $table->string('status', 30)->default('Disponível');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chave');
    }
};
