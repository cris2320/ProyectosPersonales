<?php

declare(strict_types=1);

namespace App\Shared\Console;

use App\Shared\Events\EventoPublicado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Worker del outbox (ADR-006, 04 §11): toma lotes con FOR UPDATE SKIP LOCKED,
 * los envía a la cola y los marca. Se ejecuta como proceso permanente (`outbox:despachar --loop`)
 * o desde el scheduler cada minuto como respaldo.
 */
final class DespacharOutbox extends Command
{
    protected $signature = 'outbox:despachar {--loop} {--lote=100} {--espera=1}';

    protected $description = 'Publica a la cola los eventos pendientes del outbox';

    public function handle(): int
    {
        do {
            $despachados = $this->despacharLote((int) $this->option('lote'));
            if ($despachados === 0 && $this->option('loop')) {
                sleep((int) $this->option('espera'));
            }
        } while ($this->option('loop'));

        return self::SUCCESS;
    }

    private function despacharLote(int $lote): int
    {
        return DB::transaction(function () use ($lote): int {
            $filas = DB::table('sys_outbox')
                ->whereNull('despachado_en')
                ->where('intentos', '<', 10)
                ->orderBy('id')
                ->limit($lote)
                ->lockForUpdate()
                ->get();

            // MySQL 8: SKIP LOCKED evita que varios workers se bloqueen entre sí.
            // Laravel no expone SKIP LOCKED directamente; se usa raw cuando hay más de un worker:
            // ->lock('FOR UPDATE SKIP LOCKED')

            foreach ($filas as $fila) {
                try {
                    EventoPublicado::dispatch([
                        'event_id' => $fila->event_uid,
                        'nombre' => $fila->nombre,
                        'version' => (int) $fila->version,
                        'agregado_tipo' => $fila->agregado_tipo,
                        'agregado_uid' => $fila->agregado_uid,
                        'ocurrido_en' => $fila->ocurrido_en,
                        'payload' => json_decode((string) $fila->payload, true, 512, JSON_THROW_ON_ERROR),
                    ])->onQueue('eventos');

                    DB::table('sys_outbox')->where('id', $fila->id)->update(['despachado_en' => now(), 'error' => null]);
                } catch (Throwable $e) {
                    DB::table('sys_outbox')->where('id', $fila->id)->update([
                        'intentos' => $fila->intentos + 1,
                        'error' => mb_substr($e->getMessage(), 0, 255),
                    ]);
                    report($e);
                }
            }

            return $filas->count();
        });
    }
}
