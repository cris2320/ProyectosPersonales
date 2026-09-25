<?php

declare(strict_types=1);

namespace App\Shared\Events;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Garantiza que un consumidor procese cada event_id UNA sola vez (tabla sys_eventos_procesados).
 * Uso: `use ConsumidorIdempotente;` y llamar $this->unaVez($evento, fn () => ...).
 */
trait ConsumidorIdempotente
{
    /** @param array<string, mixed> $evento */
    protected function unaVez(array $evento, callable $trabajo): void
    {
        $consumidor = static::class;

        DB::transaction(function () use ($evento, $consumidor, $trabajo): void {
            try {
                DB::table('sys_eventos_procesados')->insert([
                    'consumidor' => $consumidor,
                    'event_uid' => $evento['event_id'],
                    'procesado_en' => now(),
                ]);
            } catch (QueryException $e) {
                if ((string) $e->getCode() === '23000') {
                    return; // ya procesado: duplicado entregado por la cola
                }
                throw $e;
            }

            $trabajo();
        });
    }
}
