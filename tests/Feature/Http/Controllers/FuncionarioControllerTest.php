<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FuncionarioControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_funcionario_routes_are_requested_without_a_token(): void
    {
        $response = $this->getJson('/api/v1/funcionarios');

        $response->assertUnauthorized();
    }

    public function test_lists_funcionarios(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create(['nome' => 'Ana Funcionaria']);

        $response = $this->getJson('/api/v1/funcionarios');

        $response->assertOk()->assertJsonPath('data.0.id', $funcionario->id);
    }

    public function test_creates_funcionario_and_returns_201(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/funcionarios', [
            'nome' => 'Carlos Funcionário',
            'email' => 'carlos.funcionario@example.com',
            'cargo' => 'Técnico',
        ]);

        $response->assertCreated()->assertJsonPath('data.cargo', 'Técnico');
        $this->assertDatabaseHas('funcionario', ['email' => 'carlos.funcionario@example.com']);
    }

    public function test_shows_funcionario(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();

        $response = $this->getJson('/api/v1/funcionarios/'.$funcionario->id);

        $response->assertOk()->assertJsonPath('data.id', $funcionario->id);
    }

    public function test_updates_funcionario(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();

        $response = $this->patchJson('/api/v1/funcionarios/'.$funcionario->id, [
            'nome' => 'Nome Atualizado',
        ]);

        $response->assertOk()->assertJsonPath('data.nome', 'Nome Atualizado');
        $this->assertDatabaseHas('funcionario', [
            'id' => $funcionario->id,
            'nome' => 'Nome Atualizado',
        ]);
    }

    public function test_deletes_funcionario_and_returns_204(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();

        $response = $this->deleteJson('/api/v1/funcionarios/'.$funcionario->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('funcionario', ['id' => $funcionario->id]);
    }

    public function test_returns_409_when_deleting_funcionario_with_emprestimos(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();
        Emprestimo::factory()->create(['funcionario_id' => $funcionario->id]);

        $response = $this->deleteJson('/api/v1/funcionarios/'.$funcionario->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('funcionario', ['id' => $funcionario->id]);
    }

    public function test_returns_422_when_funcionario_fields_are_missing(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/funcionarios', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['nome', 'email']);
    }
}
