<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_emprestimo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emprestimo_id')
                ->constrained('emprestimo');

            $table->string('acao', 100);
            $table->dateTime('data_hora')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_emprestimo');
    }
};
