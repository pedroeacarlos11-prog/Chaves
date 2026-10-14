<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\LogEmprestimo;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LogEmprestimoControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_log_routes_are_requested_without_a_token(): void
    {
        $response = $this->getJson('/api/v1/logs-emprestimos');

        $response->assertUnauthorized();
    }

    public function test_lists_logs(): void
    {
        $this->authenticateAsUsuario();
        $logEmprestimo = LogEmprestimo::factory()->create();

        $response = $this->getJson('/api/v1/logs-emprestimos');

        $response->assertOk()->assertJsonPath('data.0.id', $logEmprestimo->id);
    }

    public function test_creates_log_for_an_existing_emprestimo(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $response = $this->postJson('/api/v1/logs-emprestimos', [
            'emprestimo_id' => $emprestimo->id,
            'acao' => 'Devolução registrada',
            'data_hora' => '2026-02-10 12:30:00',
        ]);

        $response->assertCreated()->assertJsonPath('data.acao', 'Devolução registrada');
        $this->assertDatabaseHas('log_emprestimo', [
            'emprestimo_id' => $emprestimo->id,
            'acao' => 'Devolução registrada',
            'data_hora' => '2026-02-10 12:30:00',
        ]);
    }

    public function test_shows_log(): void
    {
        $this->authenticateAsUsuario();
        $logEmprestimo = LogEmprestimo::factory()->create();

        $response = $this->getJson('/api/v1/logs-emprestimos/'.$logEmprestimo->id);

        $response->assertOk()->assertJsonPath('data.id', $logEmprestimo->id);
    }

    public function test_updates_log_action(): void
    {
        $this->authenticateAsUsuario();
        $logEmprestimo = LogEmprestimo::factory()->create();

        $response = $this->patchJson('/api/v1/logs-emprestimos/'.$logEmprestimo->id, [
            'acao' => 'Registro corrigido',
        ]);

        $response->assertOk()->assertJsonPath('data.acao', 'Registro corrigido');
        $this->assertDatabaseHas('log_emprestimo', [
            'id' => $logEmprestimo->id,
            'acao' => 'Registro corrigido',
        ]);
    }

    public function test_deletes_log_and_returns_204(): void
    {
        $this->authenticateAsUsuario();
        $logEmprestimo = LogEmprestimo::factory()->create();

        $response = $this->deleteJson('/api/v1/logs-emprestimos/'.$logEmprestimo->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('log_emprestimo', ['id' => $logEmprestimo->id]);
    }

    public function test_returns_422_when_log_required_fields_are_missing(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/logs-emprestimos', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['emprestimo_id', 'acao']);
    }
}
