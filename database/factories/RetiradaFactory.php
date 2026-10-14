<?php

namespace Database\Factories;

use App\Models\Retirada;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Retirada>
 */
class RetiradaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'bloco' => fake()->bothify('Bloco ##'),
            'sala' => fake()->bothify('Sala ###'),
            'data_retirada' => fake()->date(),
            'hora_retirada' => '08:30:00',
            'hora_devolucao' => null,
            'status' => 'em_uso',
        ];
    }
}
