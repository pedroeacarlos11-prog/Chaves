<?php

namespace Database\Factories;

use App\Models\TokenUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TokenUsuario>
 */
class TokenUsuarioFactory extends Factory
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
            'token' => hash('sha256', Str::random(64)),
            'valido_ate' => now()->addDays(30),
        ];
    }
}
