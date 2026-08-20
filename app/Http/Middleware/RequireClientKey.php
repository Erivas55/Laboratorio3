<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RequireClientKey
 *
 * Exige la presencia de la cabecera X-Lab-Key con el valor esperado
 * (PW2-2026) antes de permitir que la petición continúe.
 *
 */
class RequireClientKey
{
    private const EXPECTED_KEY = 'PW2-2026';

    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-Lab-Key');

        // Caso 1: el header no existe en absoluto.
        if (is_null($providedKey)) {
            logger('AUTH: rechazada - X-Lab-Key ausente');

            return $this->unauthorized();
        }

        // Caso 2: el header existe pero el valor no coincide.
        if (! hash_equals(self::EXPECTED_KEY, $providedKey)) {
            logger('AUTH: rechazada - X-Lab-Key inválida');

            return $this->unauthorized();
        }

        // Caso 3: credencial válida.
        logger('AUTH: credencial válida - petición autorizada');

        return $next($request);
    }

    private function unauthorized(): Response
    {
        return response()->json([
            'error' => 'No autorizado',
            'message' => 'Se requiere una credencial X-Lab-Key válida.',
        ], 401);
    }
}