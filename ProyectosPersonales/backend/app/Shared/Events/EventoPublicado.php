<?php

declare(strict_types=1);

namespace App\Shared\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Job que viaja por la cola (Redis) con el evento ya sacado del outbox.
 * Entrega el evento a cada consumidor registrado; la idempotencia la aporta ConsumidorIdempotente.
 */
final class EventoPublicado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @var array<int, int> segundos entre reintentos */
    public array $backoff = [10, 30, 120, 600];

    /** @param array<string, mixed> $evento */
    public function __construct(public readonly array $evento) {}

    public function handle(RegistroConsumidores $registro): void
    {
        foreach ($registro->consumidoresDe($this->evento['nombre'], (int) $this->evento['version']) as $consumidor) {
            try {
                app($consumidor)->manejar($this->evento);
            } catch (Throwable $e) {
                // Un consumidor que falla no debe impedir a los demás; se reintenta el job completo,
                // y los que ya procesaron lo detectan por sys_eventos_procesados.
                report($e);
                throw $e;
            }
        }
    }

    public function uniqueId(): string
    {
        return $this->evento['event_id'];
    }
}
