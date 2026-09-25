<?php

declare(strict_types=1);

namespace App\Shared\Idempotency;

use App\Shared\Http\ProblemDetails;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware `idempotente` (03 §8.4): exige Idempotency-Key en escrituras y cachea la respuesta 24 h
 * en sys_idempotencia. Misma clave + mismo cuerpo → misma respuesta. Misma clave + cuerpo distinto → 422.
 */
final class IdempotencyKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $clave = (string) $request->header('Idempotency-Key', '');
        if ($clave === '' || mb_strlen($clave) < 26 || mb_strlen($clave) > 80) {
            return ProblemDetails::respuesta(422, 'datos_invalidos', 'Falta la cabecera Idempotency-Key (26–80 caracteres).');
        }

        $actor = $this->actor($request);
        $hash = hash('sha256', $request->getContent());

        $previa = DB::table('sys_idempotencia')->where('clave', $clave)->where('actor_uid', $actor)->first();
        if ($previa !== null) {
            if ($previa->cuerpo_hash !== $hash) {
                return ProblemDetails::respuesta(422, 'datos_invalidos', 'Idempotency-Key reutilizada con un cuerpo distinto.');
            }

            return response((string) $previa->respuesta, (int) $previa->status_respuesta)
                ->header('Content-Type', 'application/json')
                ->header('Idempotent-Replayed', 'true');
        }

        $respuesta = $next($request);

        if ($respuesta->getStatusCode() < 500) {
            DB::table('sys_idempotencia')->insertOrIgnore([
                'clave' => $clave,
                'actor_uid' => $actor,
                'ruta' => mb_substr($request->method().' '.$request->path(), 0, 120),
                'cuerpo_hash' => $hash,
                'status_respuesta' => $respuesta->getStatusCode(),
                'respuesta' => $respuesta->getContent(),
                'expira_en' => now()->addDay(),
                'creado_en' => now(),
            ]);
        }

        return $respuesta;
    }

    private function actor(Request $request): string
    {
        $usuario = $request->user();
        if ($usuario !== null && isset($usuario->uid)) {
            return (string) $usuario->uid;
        }

        // Invitados de la tienda: se agrupa por IP hasheada para que la clave no colisione entre personas.
        return 'anon:'.hash('sha256', (string) $request->ip());
    }
}
