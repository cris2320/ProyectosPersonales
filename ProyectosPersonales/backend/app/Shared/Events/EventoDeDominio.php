<?php

declare(strict_types=1);

namespace App\Shared\Events;

use DateTimeImmutable;
use Symfony\Component\Uid\Ulid;

/**
 * Base de todo evento de dominio (ADR-006). Inmutable, nombrado en pasado, versionado.
 * Cada módulo extiende esta clase en su carpeta Events/ y define nombre(), version() y payload().
 */
abstract class EventoDeDominio
{
    public readonly string $eventId;

    public readonly DateTimeImmutable $ocurridoEn;

    public function __construct(
        public readonly string $agregadoUid,
        ?string $eventId = null,
        ?DateTimeImmutable $ocurridoEn = null,
    ) {
        $this->eventId = $eventId ?? (string) new Ulid;
        $this->ocurridoEn = $ocurridoEn ?? new DateTimeImmutable;
    }

    /** Ej.: "PedidoCreado". Debe coincidir con contracts/events/<nombre>.v<version>.json */
    abstract public static function nombre(): string;

    abstract public static function version(): int;

    /** Ej.: "Pedido" */
    abstract public static function agregadoTipo(): string;

    /** @return array<string, mixed> Solo datos serializables a JSON. */
    abstract public function payload(): array;

    /** @return array<string, mixed> Envoltura estándar que viaja por el outbox y la cola. */
    public function aArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'nombre' => static::nombre(),
            'version' => static::version(),
            'agregado_tipo' => static::agregadoTipo(),
            'agregado_uid' => $this->agregadoUid,
            'ocurrido_en' => $this->ocurridoEn->format(DATE_RFC3339_EXTENDED),
            'payload' => $this->payload(),
        ];
    }
}
