<?php

namespace Tests\Feature;

use App\Models\TokenUsuario;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TokenUsuarioControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_token_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/tokens-usuario')->assertUnauthorized();
    }

    public function test_user_can_create_a_sanctum_token_that_authenticates_requests(): void
    {
        $usuario = $this->authenticateAsUsuario();

        $response = $this->postJson('/api/v1/tokens-usuario', []);

        $response->assertCreated()
            ->assertJsonPath('token_usuario.usuario_id', $usuario->id)
            ->assertJsonPath('token_type', 'Bearer');

        $plainToken = $response->json('access_token');
        $this->assertIsString($plainToken);
        [$personalAccessTokenId, $tokenSecret] = explode('|', $plainToken, 2);
        $this->assertIsNumeric($personalAccessTokenId);

        $tokenHash = hash('sha256', $tokenSecret);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $personalAccessTokenId,
            'tokenable_type' => Usuario::class,
            'tokenable_id' => $usuario->id,
            'token' => $tokenHash,
        ]);
        $this->assertDatabaseHas('token_usuario', [
            'usuario_id' => $usuario->id,
            'token' => $tokenHash,
        ]);
        $response->assertJsonMissing(['token' => $tokenHash]);

        app('auth')->forgetGuards();
        $this->withHeader('Authorization', 'Bearer '.$plainToken)
            ->getJson('/api/v1/retiradas')
            ->assertOk();
    }

    public function test_user_can_only_list_and_show_their_own_tokens_without_secrets(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $ownToken = TokenUsuario::factory()->create(['usuario_id' => $usuario->id]);
        $anotherToken = TokenUsuario::factory()->create();

        $this->getJson('/api/v1/tokens-usuario')
            ->assertOk()
            ->assertJsonFragment(['id' => $ownToken->id])
            ->assertJsonMissing(['id' => $anotherToken->id])
            ->assertJsonMissing(['token' => $ownToken->token]);

        $this->getJson('/api/v1/tokens-usuario/'.$anotherToken->id)->assertNotFound();
        $this->getJson('/api/v1/tokens-usuario/'.$ownToken->id)
            ->assertOk()
            ->assertJsonMissing(['token' => $ownToken->token]);
    }

    public function test_user_can_update_and_delete_their_token(): void
    {
        $usuario = $this->authenticateAsUsuario();
        $personalAccessToken = $usuario->createToken('managed-token');
        $token = TokenUsuario::create([
            'usuario_id' => $usuario->id,
            'token' => $personalAccessToken->accessToken->token,
            'valido_ate' => now()->addDays(30),
        ]);
        $newExpiration = now()->addDays(60)->toISOString();

        $this->patchJson('/api/v1/tokens-usuario/'.$token->id, [
            'valido_ate' => $newExpiration,
        ])->assertOk();

        $this->assertSame(
            now()->addDays(60)->toDateString(),
            $token->refresh()->valido_ate->toDateString(),
        );
        $this->assertSame(
            now()->addDays(60)->toDateString(),
            $personalAccessToken->accessToken->fresh()->expires_at->toDateString(),
        );

        $this->deleteJson('/api/v1/tokens-usuario/'.$token->id)->assertNoContent();
        $this->assertDatabaseMissing('token_usuario', ['id' => $token->id]);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $personalAccessToken->accessToken->id]);
    }

    public function test_logout_revokes_the_current_bearer_token(): void
    {
        $usuario = Usuario::factory()->create();
        $newAccessToken = $usuario->createToken('logout-test');
        TokenUsuario::create([
            'usuario_id' => $usuario->id,
            'token' => $newAccessToken->accessToken->token,
            'valido_ate' => now()->addDays(30),
        ]);
        $plainToken = $newAccessToken->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$plainToken);

        $this->postJson('/api/v1/logout')->assertNoContent();
        app('auth')->forgetGuards();
        $this->withHeader('Authorization', 'Bearer '.$plainToken)
            ->getJson('/api/v1/retiradas')
            ->assertUnauthorized();
    }

    public function test_login_issues_a_sanctum_bearer_token(): void
    {
        $usuario = Usuario::factory()->create(['senha' => bcrypt('SenhaTeste123!')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $usuario->email,
            'senha' => 'SenhaTeste123!',
        ]);

        $response->assertOk()->assertJsonStructure(['access_token', 'token_type', 'expires_at', 'usuario']);
        $plainToken = $response->json('access_token');
        [$personalAccessTokenId, $tokenSecret] = explode('|', $plainToken, 2);
        $tokenHash = hash('sha256', $tokenSecret);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $personalAccessTokenId,
            'tokenable_type' => Usuario::class,
            'tokenable_id' => $usuario->id,
            'token' => $tokenHash,
        ]);
        $this->assertDatabaseHas('token_usuario', [
            'usuario_id' => $usuario->id,
            'token' => $tokenHash,
        ]);

        app('auth')->forgetGuards();
        $this->withHeader('Authorization', 'Bearer '.$plainToken)
            ->getJson('/api/v1/tokens-usuario')
            ->assertOk();
    }
}
