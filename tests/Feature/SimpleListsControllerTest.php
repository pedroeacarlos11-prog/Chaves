<?php

namespace Tests\Feature;

use App\Models\Chave;
use App\Models\Emprestimo;
use App\Models\Funcionario;
use App\Models\LogEmprestimo;
use App\Models\Retirada;
use App\Models\TokenUsuario;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SimpleListsControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_simple_usuario_list_only_returns_public_contact_fields(): void
    {
        $this->authenticateAsUsuario();
        Usuario::factory()->create([
            'nome' => 'Usuária da lista simples',
            'email' => 'lista.simples@example.test',
            'cpf' => '12345678900',
        ]);

        $this->getJson('/api/v1/usuarios/simples')
            ->assertOk()
            ->assertJsonFragment([
                'nome' => 'Usuária da lista simples',
                'email' => 'lista.simples@example.test',
            ])
            ->assertJsonMissing(['cpf' => '12345678900'])
            ->assertJsonMissingPath('data.0.senha');
    }

    public function test_simple_funcionario_list_returns_the_selected_fields(): void
    {
        $this->authenticateAsUsuario();
        Funcionario::factory()->create([
            'nome' => 'Funcionário da lista simples',
            'email' => 'funcionario.simples@example.test',
            'cargo' => 'Técnico',
        ]);

        $this->getJson('/api/v1/funcionarios/simples')
            ->assertOk()
            ->assertJsonFragment([
                'nome' => 'Funcionário da lista simples',
                'email' => 'funcionario.simples@example.test',
                'cargo' => 'Técnico',
            ]);
    }

    public function test_simple_chave_list_returns_the_selected_fields(): void
    {
        $this->authenticateAsUsuario();
        Chave::factory()->create([
            'nome_chave' => 'Chave da lista simples',
            'localizacao' => 'Laboratório',
            'status' => 'Disponível',
        ]);

        $this->getJson('/api/v1/chaves/simples')
            ->assertOk()
            ->assertJsonFragment([
                'nome_chave' => 'Chave da lista simples',
                'status' => 'Disponível',
            ])
            ->assertJsonMissingPath('data.0.localizacao');
    }

    public function test_simple_emprestimo_list_returns_relationship_ids_and_status(): void
    {
        $this->authenticateAsUsuario();
        $emprestimo = Emprestimo::factory()->create();

        $this->getJson('/api/v1/emprestimos/simples')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $emprestimo->id,
                'funcionario_id' => $emprestimo->funcionario_id,
                'chave_id' => $emprestimo->chave_id,
                'status' => 'Em andamento',
            ])
            ->assertJsonMissingPath('data.0.data_retirada');
    }

    public function test_simple_log_list_returns_the_selected_fields(): void
    {
        $this->authenticateAsUsuario();
        $log = LogEmprestimo::factory()->create(['acao' => 'Ação da lista simples']);

        $this->getJson('/api/v1/logs-emprestimos/simples')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $log->id,
                'emprestimo_id' => $log->emprestimo_id,
                'acao' => 'Ação da lista simples',
            ]);
    }

    public function test_simple_retirada_list_only_returns_the_authenticated_users_records(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $ownRetirada = Retirada::factory()->create([
            'usuario_id' => $usuario->id,
            'bloco' => 'Bloco próprio',
        ]);
        Retirada::factory()->create(['bloco' => 'Bloco de outra conta']);

        $this->getJson('/api/v1/retiradas/simples')
            ->assertOk()
            ->assertJsonFragment(['id' => $ownRetirada->id, 'bloco' => 'Bloco próprio'])
            ->assertJsonMissing(['bloco' => 'Bloco de outra conta']);
    }

    public function test_simple_token_list_is_scoped_and_does_not_expose_token_values(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $ownToken = TokenUsuario::factory()->create(['usuario_id' => $usuario->id]);
        $otherToken = TokenUsuario::factory()->create();

        $this->getJson('/api/v1/tokens-usuario/simples')
            ->assertOk()
            ->assertJsonFragment(['id' => $ownToken->id, 'usuario_id' => $usuario->id])
            ->assertJsonMissing(['id' => $otherToken->id])
            ->assertJsonMissing(['token' => $ownToken->token]);
    }

    public function test_legacy_list_aliases_call_each_model_controller(): void
    {
        $this->authenticateAsUsuario();

        foreach ([
            'listar_usuarios',
            'listar_usuarios_simples',
            'listar_funcionarios',
            'listar_funcionarios_simples',
            'listar_chaves',
            'listar_chaves_simples',
            'listar_emprestimos',
            'listar_emprestimos_simples',
            'listar_logs_emprestimos',
            'listar_logs_emprestimos_simples',
            'listar_retiradas',
            'listar_retiradas_simples',
            'listar_tokens_usuario',
            'listar_tokens_usuario_simples',
        ] as $path) {
            $this->getJson('/api/v1/'.$path)->assertOk();
        }
    }
}
