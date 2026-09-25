<?php

declare(strict_types=1);

namespace App\Shared\Http;

use DomainException;

/**
 * Base para errores de negocio que llegan al cliente con código estable.
 * Los módulos extienden: final class StockInsuficiente extends ExcepcionDeDominio { codigo 'stock_insuficiente', status 409 }
 */
abstract class ExcepcionDeDominio extends DomainException
{
    /** @var array<string, mixed> */
    protected array $extra = [];

    abstract public function codigo(): string;

    public function status(): int
    {
        return 409;
    }

    /** @return array<string, mixed> Datos adicionales para el frontend (p. ej. skus afectados). */
    public function extra(): array
    {
        return $this->extra;
    }
}
