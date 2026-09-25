<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/** Propaga o genera X-Trace-Id y lo añade al contexto de logs (03 §8.5). */
final class TraceId
{
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = (string) $request->header('X-Trace-Id', bin2hex(random_bytes(16)));
        $request->attributes->set('trace_id', $traceId);
        Log::withContext(['trace_id' => $traceId]);

        $respuesta = $next($request);
        $respuesta->headers->set('X-Trace-Id', $traceId);

        return $respuesta;
    }
}
