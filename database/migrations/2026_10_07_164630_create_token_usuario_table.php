<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('token_usuario')) {
            Schema::create('token_usuario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
                $table->string('token')->unique();
                $table->timestamp('valido_ate');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_usuario');
    }
};
