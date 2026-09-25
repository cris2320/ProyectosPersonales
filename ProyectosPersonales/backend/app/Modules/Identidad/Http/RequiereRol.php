<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http;

use App\Shared\Http\ProblemDetails;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Middleware `rol:administrador,almacen`. Verifica la habilidad del token Sanctum (rol:*). */
final class RequiereRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $u = $request->user('personal');
        if ($u === null) {
            return ProblemDetails::respuesta(401, 'no_autenticado', 'Inicia sesión para continuar.');
        }
        foreach ($roles as $rol) {
            if ($u->tokenCan('rol:'.$rol)) {
                return $next($request);
            }
        }

        return ProblemDetails::respuesta(403, 'no_autorizado', 'Tu rol no permite esta acción.');
    }
}
