<?php

namespace Tests\Feature;

use App\Models\Retirada;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RetiradaControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_retirada_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/retiradas')->assertUnauthorized();
    }

    public function test_user_can_create_a_retirada_owned_by_the_authenticated_user(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $anotherUsuario = Usuario::factory()->create();

        $response = $this->postJson('/api/v1/retiradas', [
            'usuario_id' => $anotherUsuario->id,
            'bloco' => 'Bloco A',
            'sala' => 'Sala 12',
            'data_retirada' => '2026-10-07',
            'hora_retirada' => '08:30:00',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.usuario_id', $usuario->id)
            ->assertJsonPath('data.status', 'em_uso');

        $this->assertDatabaseHas('retiradas', [
            'usuario_id' => $usuario->id,
            'bloco' => 'Bloco A',
            'sala' => 'Sala 12',
        ]);
    }

    public function test_user_only_sees_their_own_retiradas(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $ownRetirada = Retirada::factory()->create(['usuario_id' => $usuario->id]);
        $anotherRetirada = Retirada::factory()->create();

        $response = $this->getJson('/api/v1/retiradas');

        $response->assertOk()
            ->assertJsonFragment(['id' => $ownRetirada->id])
            ->assertJsonMissing(['id' => $anotherRetirada->id]);
    }

    public function test_user_can_show_update_and_delete_their_retirada(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $retirada = Retirada::factory()->create(['usuario_id' => $usuario->id]);

        $this->getJson('/api/v1/retiradas/'.$retirada->id)
            ->assertOk()
            ->assertJsonPath('data.id', $retirada->id);

        $this->patchJson('/api/v1/retiradas/'.$retirada->id, [
            'status' => 'devolvida',
            'hora_devolucao' => '10:45:00',
        ])->assertOk()->assertJsonPath('data.status', 'devolvida');

        $this->deleteJson('/api/v1/retiradas/'.$retirada->id)->assertNoContent();
        $this->assertDatabaseMissing('retiradas', ['id' => $retirada->id]);
    }

    public function test_user_cannot_access_another_users_retirada(): void
    {
        $this->authenticateAsUsuario();
        $retirada = Retirada::factory()->create();

        $this->getJson('/api/v1/retiradas/'.$retirada->id)->assertNotFound();
    }

    public function test_retirada_creation_validates_required_fields(): void
    {
        $this->authenticateAsUsuario();

        $this->postJson('/api/v1/retiradas', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['bloco', 'sala', 'data_retirada', 'hora_retirada']);
    }
}
