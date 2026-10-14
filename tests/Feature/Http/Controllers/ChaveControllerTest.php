<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chave;
use App\Models\Emprestimo;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ChaveControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_chave_routes_are_requested_without_a_token(): void
    {
        $response = $this->getJson('/api/v1/chaves');

        $response->assertUnauthorized();
    }

    public function test_lists_chaves(): void
    {
        $this->authenticateAsUsuario();
        $chave = Chave::factory()->create(['nome_chave' => 'Chave da Biblioteca']);

        $response = $this->getJson('/api/v1/chaves');

        $response->assertOk()->assertJsonPath('data.0.id', $chave->id);
    }

    public function test_creates_chave_with_the_default_status(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/chaves', [
            'nome_chave' => 'Chave do Laboratório',
            'localizacao' => 'Bloco A',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'Disponível');
        $this->assertDatabaseHas('chave', [
            'nome_chave' => 'Chave do Laboratório',
            'status' => 'Disponível',
        ]);
    }

    public function test_shows_chave(): void
    {
        $this->authenticateAsUsuario();
        $chave = Chave::factory()->create();

        $response = $this->getJson('/api/v1/chaves/'.$chave->id);

        $response->assertOk()->assertJsonPath('data.id', $chave->id);
    }

    public function test_updates_chave(): void
    {
        $this->authenticateAsUsuario();
        $chave = Chave::factory()->create();

        $response = $this->patchJson('/api/v1/chaves/'.$chave->id, [
            'status' => 'Indisponível',
        ]);

        $response->assertOk()->assertJsonPath('data.status', 'Indisponível');
        $this->assertDatabaseHas('chave', [
            'id' => $chave->id,
            'status' => 'Indisponível',
        ]);
    }

    public function test_deletes_chave_and_returns_204(): void
    {
        $this->authenticateAsUsuario();
        $chave = Chave::factory()->create();

        $response = $this->deleteJson('/api/v1/chaves/'.$chave->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('chave', ['id' => $chave->id]);
    }

    public function test_returns_409_when_deleting_chave_with_emprestimos(): void
    {
        $this->authenticateAsUsuario();
        $chave = Chave::factory()->create();
        Emprestimo::factory()->create(['chave_id' => $chave->id]);

        $response = $this->deleteJson('/api/v1/chaves/'.$chave->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('chave', ['id' => $chave->id]);
    }

    public function test_returns_422_when_chave_name_is_missing(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/chaves', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['nome_chave']);
    }
}
