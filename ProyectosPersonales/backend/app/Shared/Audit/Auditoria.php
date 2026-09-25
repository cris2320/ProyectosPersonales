<?php

declare(strict_types=1);

namespace App\Shared\Audit;

use Illuminate\Support\Facades\DB;

/** Escribe en sys_auditoria (04 §11). Nunca guardar datos personales en claro en antes/despues. */
final class Auditoria
{
    /**
     * @param array<string, mixed>|null $antes
     * @param array<string, mixed>|null $despues
     */
    public static function registrar(
        string $accion,
        string $entidadTipo,
        string $entidadUid,
        ?array $antes = null,
        ?array $despues = null,
        ?string $actorUid = null,
        string $actorTipo = 'usuario',
    ): void {
        $request = request();
        DB::table('sys_auditoria')->insert([
            'actor_tipo' => $actorUid === null ? 'sistema' : $actorTipo,
            'actor_uid' => $actorUid,
            'accion' => $accion,
            'entidad_tipo' => $entidadTipo,
            'entidad_uid' => $entidadUid,
            'antes' => $antes === null ? null : json_encode($antes, JSON_UNESCAPED_UNICODE),
            'despues' => $despues === null ? null : json_encode($despues, JSON_UNESCAPED_UNICODE),
            'ip_hash' => $request?->ip() ? hash('sha256', (string) $request->ip()) : null,
            'trace_id' => $request?->attributes->get('trace_id'),
            'creado_en' => now(),
        ]);
    }
}
