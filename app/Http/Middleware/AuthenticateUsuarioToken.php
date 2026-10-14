<?php

namespace App\Http\Middleware;

use App\Models\TokenUsuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUsuarioToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! is_string($plainToken) || $plainToken === '') {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = TokenUsuario::query()
            ->with('usuario')
            ->where('token', hash('sha256', $plainToken))
            ->where('valido_ate', '>', now())
            ->first();

        if (! $token) {
            $token = TokenUsuario::query()
                ->with('usuario')
                ->where('token', $plainToken)
                ->where('valido_ate', '>', now())
                ->first();

            if ($token) {
                $token->update(['token' => hash('sha256', $plainToken)]);
            }
        }

        if (! $token || ! $token->usuario) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $request->attributes->set('usuario_token', $token);
        $request->setUserResolver(fn () => $token->usuario);

        return $next($request);
    }
}
