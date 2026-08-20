<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * TraceRequest
 *
 * Problema que resuelve: cuando varias peticiones generan logs de forma
 * simultánea, es imposible saber qué eventos de log pertenecen a una misma
 * petición si no existe un identificador común entre ellos.
 *
 * Este middleware genera un identificador único (Trace ID) al inicio del
 * pipeline, lo usa para amarrar los logs de "inicio" y "final" de esa
 * petición específica, y lo expone al cliente en la cabecera X-Trace-Id
 * para que la trazabilidad no dependa solo de los logs del servidor.
 */
class TraceRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Generar un identificador único para esta petición.
        $traceId = (string) Str::uuid();
        $shortTraceId = substr($traceId, 0, 8);

        $request->attributes->set('trace_id', $shortTraceId);

        // 2. Registrar el inicio de la petición.
        logger("[{$shortTraceId}] TRACE: inicio de petición - {$request->method()} {$request->fullUrl()}");

        // 3. Permitir que la petición continúe por el resto del pipeline.
        $response = $next($request);

        // 4. Ya tenemos la Response de regreso.

        // 5. Agregar el Trace ID a la Response.
        $response->headers->set('X-Trace-Id', $shortTraceId);

        // 6. Registrar el final de la petición.
        logger("[{$shortTraceId}] TRACE: fin de petición - status {$response->getStatusCode()}");

        // 7. Retornar la Response hacia el Middleware anterior.
        return $response;
    }
}