<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TraceRequest
{
    private const MAX_INCOMING_LENGTH = 64;

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Determinar el Trace ID: si el cliente ya mandó uno válido en
        //    X-Trace-Id, lo reutilizamos (Desafío 16); si no, generamos uno.
        $shortTraceId = $this->resolveTraceId($request);

        $request->attributes->set('trace_id', $shortTraceId);

        logger("[{$shortTraceId}] TRACE: inicio de petición - {$request->method()} {$request->fullUrl()}");

        $response = $next($request);

        $response->headers->set('X-Trace-Id', $shortTraceId);

        logger("[{$shortTraceId}] TRACE: fin de petición - status {$response->getStatusCode()}");

        return $response;
    }

    private function resolveTraceId(Request $request): string
    {
        $incoming = $request->header('X-Trace-Id');

        if ($this->isValidIncomingTraceId($incoming)) {
            return $incoming;
        }

        $traceId = (string) Str::uuid();

        return substr($traceId, 0, 8);
    }

    private function isValidIncomingTraceId(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        if (strlen($value) > self::MAX_INCOMING_LENGTH) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9\-]+$/', $value);
    }
}