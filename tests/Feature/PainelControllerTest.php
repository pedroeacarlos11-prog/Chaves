<?php

namespace Tests\Feature;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PainelControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_management_pages_redirect_guests_to_login(): void
    {
        $this->get('/chaves')->assertRedirectToRoute('login');
    }

    public function test_employee_page_lists_records_from_the_database(): void
    {
        $this->actingAs(User::factory()->create());
        $funcionario = Funcionario::factory()->create(['nome' => 'Funcionário cadastrado']);

        $this->get('/funcionarios')
            ->assertOk()
            ->assertSee('Funcionário cadastrado')
            ->assertSee('data-view-resource="funcionario"', false);
    }

    public function test_web_loan_creation_and_return_update_the_key_status(): void
    {
        $this->actingAs(User::factory()->create());
        $funcionario = Funcionario::factory()->create();
        $chave = Chave::factory()->create(['status' => 'Disponível']);

        $this->from('/emprestimos')->post(route('painel.emprestimos.store'), [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-10-19',
            'hora_retirada' => '08:30',
        ])->assertRedirectBackWithoutErrors();

        $emprestimo = Emprestimo::query()->firstOrFail();

        $this->assertDatabaseHas('chave', [
            'id' => $chave->id,
            'status' => 'Emprestada',
        ]);

        $this->from('/emprestimos')->put(route('painel.emprestimos.update', $emprestimo), [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-10-19',
            'hora_retirada' => '08:30',
            'data_devolucao' => '2026-10-19',
            'hora_devolucao' => '10:15',
            'status' => 'Devolvido',
        ])->assertRedirectBackWithoutErrors();

        $this->assertDatabaseHas('emprestimo', [
            'id' => $emprestimo->id,
            'status' => 'Devolvido',
            'hora_devolucao' => '10:15',
        ]);
        $this->assertDatabaseHas('chave', [
            'id' => $chave->id,
            'status' => 'Disponível',
        ]);
    }

    public function test_web_loan_creation_rejects_a_key_with_an_active_loan(): void
    {
        $this->actingAs(User::factory()->create());
        $chave = Chave::factory()->create(['status' => 'Disponível']);
        Emprestimo::factory()->create([
            'chave_id' => $chave->id,
            'status' => 'Em andamento',
        ]);
        $funcionario = Funcionario::factory()->create();

        $this->from('/emprestimos')->post(route('painel.emprestimos.store'), [
            'funcionario_id' => $funcionario->id,
            'chave_id' => $chave->id,
            'data_retirada' => '2026-10-19',
            'hora_retirada' => '09:00',
        ])->assertRedirectBackWithErrors(['chave_id']);

        $this->assertSame(1, Emprestimo::query()->where('chave_id', $chave->id)->count());
    }
}
