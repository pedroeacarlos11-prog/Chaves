<?php

namespace Database\Factories;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Emprestimo>
 */
class EmprestimoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'funcionario_id' => Funcionario::factory(),
            'chave_id' => Chave::factory(),
            'data_retirada' => '2026-01-15',
            'hora_retirada' => '08:30:00',
            'data_devolucao' => null,
            'hora_devolucao' => null,
            'status' => 'Em andamento',
        ];
    }
}
