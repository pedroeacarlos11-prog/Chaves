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
        Schema::table('funcionario', function (Blueprint $table) {
            $table->string('nome')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('cargo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionario', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['nome', 'email', 'cargo']);
        });
    }
};
