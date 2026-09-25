<?php

declare(strict_types=1);

namespace App\Shared\Events;

use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Inserta el evento en el outbox DENTRO de la transacción de negocio (ADR-006).
 * Nunca publica directo a la cola: eso lo hace el comando outbox:despachar.
 */
final class PublicadorEventos
{
    public function publicar(EventoDeDominio ...$eventos): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('Los eventos de dominio solo se publican dentro de una transacción.');
        }

        foreach ($eventos as $evento) {
            $datos = $evento->aArray();
            Outbox::query()->insert([
                'event_uid' => $datos['event_id'],
                'nombre' => $datos['nombre'],
                'version' => $datos['version'],
                'agregado_tipo' => $datos['agregado_tipo'],
                'agregado_uid' => $datos['agregado_uid'],
                'payload' => json_encode($datos['payload'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'ocurrido_en' => $evento->ocurridoEn->format('Y-m-d H:i:s.v'),
                'intentos' => 0,
            ]);
        }
    }
}
