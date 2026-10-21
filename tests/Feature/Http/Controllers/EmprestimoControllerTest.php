<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use App\Models\LogEmprestimo;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class EmprestimoControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_emprestimo_routes_are_requested_without_a_token(): void
    {
        $response = $this->getJson('/api/v1/emprestimos');

        $response->assertUnauthorized();
    }

    public function test_lists_emprestimos(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $response = $this->getJson('/api/v1/emprestimos');

        $response->assertOk()->assertJsonPath('data.0.id', $emprestimo->id);
    }

    public function test_creates_emprestimo_with_valid_foreign_keys(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();
        $chave = Chave::factory()->create();

        $response = $this->postJson('/api/v1/emprestimos', [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-02-10',
            'hora_retirada' => '08:30',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'Em andamento');
        $this->assertDatabaseHas('emprestimo', [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-02-10',
        ]);
        $this->assertDatabaseHas('chave', [
            'id' => $chave->id,
            'status' => 'Emprestada',
        ]);
    }

    public function test_shows_emprestimo(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $response = $this->getJson('/api/v1/emprestimos/'.$emprestimo->id);

        $response->assertOk()->assertJsonPath('data.id', $emprestimo->id);
    }

    public function test_updates_emprestimo_status_and_return_time(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $response = $this->patchJson('/api/v1/emprestimos/'.$emprestimo->id, [
            'status' => 'Devolvido',
            'data_devolucao' => '2026-02-10',
            'hora_devolucao' => '12:30',
        ]);

        $response->assertOk()->assertJsonPath('data.status', 'Devolvido');
        $this->assertDatabaseHas('emprestimo', [
            'id' => $emprestimo->id,
            'status' => 'Devolvido',
            'data_devolucao' => '2026-02-10',
        ]);
        $this->assertDatabaseHas('chave', [
            'id' => $emprestimo->chave_id,
            'status' => 'Disponível',
        ]);
    }

    public function test_returns_422_when_a_key_already_has_an_active_emprestimo(): void
    {
        $this->authenticateAsUsuario();
        $funcionario = Funcionario::factory()->create();
        $chave = Chave::factory()->create();
        Emprestimo::factory()->create(['chave_id' => $chave->id]);

        $response = $this->postJson('/api/v1/emprestimos', [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-02-11',
            'hora_retirada' => '09:30',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['chave_id']);
        $this->assertSame(1, Emprestimo::query()->where('chave_id', $chave->id)->count());
    }

    public function test_deletes_emprestimo_and_returns_204(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $response = $this->deleteJson('/api/v1/emprestimos/'.$emprestimo->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('emprestimo', ['id' => $emprestimo->id]);
    }

    public function test_returns_409_when_deleting_emprestimo_with_logs(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();
        LogEmprestimo::factory()->create(['emprestimo_id' => $emprestimo->id]);

        $response = $this->deleteJson('/api/v1/emprestimos/'.$emprestimo->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('emprestimo', ['id' => $emprestimo->id]);
    }

    public function test_returns_422_when_emprestimo_foreign_keys_are_missing(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/emprestimos', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['funcionario_id', 'chave_id']);
    }
}
