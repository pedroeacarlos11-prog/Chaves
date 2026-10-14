<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_usuario_routes_are_requested_without_a_token(): void
    {
        $response = $this->getJson('/api/v1/usuarios');

        $response->assertUnauthorized();
    }

    public function test_lists_usuarios_without_exposing_their_passwords(): void
    {
        $this->authenticateAsUsuario();
        $usuario = Usuario::factory()->create(['nome' => 'Maria Teste']);

        $response = $this->getJson('/api/v1/usuarios');

        $response->assertOk()
            ->assertJsonFragment(['nome' => 'Maria Teste'])
            ->assertJsonMissing(['senha' => $usuario->senha]);
    }

    public function test_creates_usuario_with_a_hashed_password(): void
    {
        $this->authenticateAsUsuario();
        $payload = [
            'nome' => 'Pedro de Teste',
            'email' => 'pedro.api@example.com',
            'senha' => 'Senha123!',
            'cpf' => '123.456.789-00',
            'data_nascimento' => '2000-01-15',
        ];

        $response = $this->postJson('/api/v1/usuarios', $payload);

        $response->assertCreated()->assertJsonPath('data.email', 'pedro.api@example.com');
        $this->assertDatabaseHas('usuario', [
            'email' => 'pedro.api@example.com',
            'cpf' => '123.456.789-00',
        ]);
        $this->assertSame(true, Hash::check('Senha123!', (string) Usuario::query()
            ->where('email', 'pedro.api@example.com')
            ->value('senha')));
    }

    public function test_returns_usuario_from_the_ver_usuario_endpoint(): void
    {
        $this->authenticateAsUsuario();
        $usuario = Usuario::factory()->create();

        $response = $this->getJson('/api/v1/ver_usuario?id='.$usuario->id);

        $response->assertOk()->assertJsonPath('usuario.id', $usuario->id);
    }

    public function test_updates_usuario_and_hashes_a_changed_password(): void
    {
        $this->authenticateAsUsuario();
        $usuario = Usuario::factory()->create();

        $response = $this->patchJson('/api/v1/usuarios/'.$usuario->id, [
            'nome' => 'Nome Atualizado',
            'senha' => 'NovaSenha123!',
        ]);

        $response->assertOk()->assertJsonPath('data.nome', 'Nome Atualizado');
        $this->assertSame(true, Hash::check('NovaSenha123!', (string) $usuario->refresh()->senha));
    }

    public function test_deletes_usuario_and_returns_204(): void
    {
        $this->authenticateAsUsuario();
        $usuario = Usuario::factory()->create();

        $response = $this->deleteJson('/api/v1/usuarios/'.$usuario->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('usuario', ['id' => $usuario->id]);
    }

    public function test_accepts_a_legacy_md5_password_and_upgrades_it_after_login(): void
    {
        $usuario = Usuario::factory()->create(['senha' => md5('SenhaLegada123!')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $usuario->email,
            'senha' => 'SenhaLegada123!',
            'device_name' => 'feature-test',
        ]);

        $response->assertOk()->assertJsonStructure(['access_token', 'token_type', 'usuario']);
        $this->assertSame(true, Hash::check('SenhaLegada123!', (string) $usuario->refresh()->senha));
        [$personalAccessTokenId, $tokenSecret] = explode('|', $response->json('access_token'), 2);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $personalAccessTokenId,
            'tokenable_id' => $usuario->id,
            'token' => hash('sha256', $tokenSecret),
        ]);
    }

    public function test_returns_422_when_usuario_payload_is_missing_required_fields(): void
    {
        $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/usuarios', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['nome', 'email', 'senha', 'data_nascimento']);
    }
}
