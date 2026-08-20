<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MeasureResponseTime
 *
 * Mide cuánto tiempo tarda en procesarse la petición desde este punto del
 * pipeline hacia adelante, y expone el resultado en X-Response-Time.
 *
 */
class MeasureResponseTime
{
    public function handle(Request $request, Closure $next): Response
    {
        $t0 = microtime(true);

        $response = $next($request);

        $t1 = microtime(true);

        $elapsedMs = ($t1 - $t0) * 1000;
        $formatted = number_format($elapsedMs, 2).'ms';

        $response->headers->set('X-Response-Time', $formatted);

        logger("TIMER: procesamiento completado en {$formatted}");

        return $response;
    }
}