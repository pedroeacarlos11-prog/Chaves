<?php

namespace Database\Factories;

use App\Models\Chave;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chave>
 */
class ChaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_chave' => fake()->unique()->bothify('Chave ##'),
            'localizacao' => fake()->city(),
            'status' => 'Disponível',
        ];
    }
}
