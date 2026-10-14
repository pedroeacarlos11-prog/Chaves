<?php

namespace Tests;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateAsUsuario(?Usuario $usuario = null): Usuario
    {
        $usuario ??= Usuario::factory()->create();
        Sanctum::actingAs($usuario, ['*']);

        return $usuario;
    }
}
