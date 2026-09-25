<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Se registra en bootstrap/app.php: ->withExceptions(fn (Exceptions $e) => ManejadorDeExcepciones::registrar($e))
 * Convierte toda excepción de la API a RFC 9457.
 */
final class ManejadorDeExcepciones
{
    public static function registrar(Exceptions $exceptions): void
    {
        $exceptions->dontReport([ExcepcionDeDominio::class]);

        $exceptions->render(function (Throwable $e, $request): ?JsonResponse {
            if (! $request->is('api/*')) {
                return null;
            }

            return match (true) {
                $e instanceof ExcepcionDeDominio => self::dominio($e),
                $e instanceof ValidationException => self::validacion($e),
                $e instanceof AuthenticationException => ProblemDetails::respuesta(401, 'no_autenticado', 'Inicia sesión para continuar.'),
                $e instanceof AuthorizationException => ProblemDetails::respuesta(403, 'no_autorizado', 'No tienes permiso para esta acción.'),
                $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => ProblemDetails::respuesta(404, 'no_encontrado', 'El recurso no existe.'),
                $e instanceof ThrottleRequestsException => ProblemDetails::respuesta(429, 'demasiadas_solicitudes', 'Espera un momento e intenta de nuevo.'),
                default => self::inesperado($e),
            };
        });
    }

    private static function dominio(ExcepcionDeDominio $e): JsonResponse
    {
        $respuesta = ProblemDetails::respuesta($e->status(), $e->codigo(), $e->getMessage());
        if ($e->extra() !== []) {
            $respuesta->setData(array_merge((array) $respuesta->getData(true), $e->extra()));
        }

        return $respuesta;
    }

    private static function validacion(ValidationException $e): JsonResponse
    {
        $errors = [];
        foreach ($e->errors() as $campo => $mensajes) {
            foreach ($mensajes as $mensaje) {
                $errors[] = ['campo' => $campo, 'mensaje' => $mensaje];
            }
        }

        return ProblemDetails::respuesta(422, 'datos_invalidos', 'Revisa los campos marcados.', $errors);
    }

    private static function inesperado(Throwable $e): JsonResponse
    {
        report($e);

        return ProblemDetails::respuesta(500, 'error_interno', 'Ocurrió un error. Ya fuimos avisados.');
    }
}
