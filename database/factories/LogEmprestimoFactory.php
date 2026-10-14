<?php

namespace Database\Factories;

use App\Models\Emprestimo;
use App\Models\LogEmprestimo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogEmprestimo>
 */
class LogEmprestimoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'emprestimo_id' => Emprestimo::factory(),
            'acao' => 'Empréstimo registrado',
            'data_hora' => '2026-01-15 08:30:00',
        ];
    }
}
