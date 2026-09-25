<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Http\JsonResponse;

/** Respuestas de error RFC 9457 (03 §8.3). */
final class ProblemDetails
{
    /** @param array<int, array{campo: string, mensaje: string}> $errors */
    public static function respuesta(int $status, string $codigo, string $detail, array $errors = []): JsonResponse
    {
        $cuerpo = [
            'type' => 'https://dtoo.pe/errores/'.str_replace('_', '-', $codigo),
            'title' => self::titulo($status),
            'status' => $status,
            'detail' => $detail,
            'codigo' => $codigo,
            'trace_id' => request()->attributes->get('trace_id'),
        ];
        if ($errors !== []) {
            $cuerpo['errors'] = $errors;
        }

        return new JsonResponse($cuerpo, $status, ['Content-Type' => 'application/problem+json']);
    }

    private static function titulo(int $status): string
    {
        return match ($status) {
            400 => 'Solicitud incorrecta',
            401 => 'No autenticado',
            403 => 'No autorizado',
            404 => 'No encontrado',
            409 => 'Conflicto',
            422 => 'Datos inválidos',
            429 => 'Demasiadas solicitudes',
            default => 'Error',
        };
    }
}
