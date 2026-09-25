<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Contracts;

final readonly class HistorialCliente
{
    public function __construct(
        public int $entregados,
        public int $rechazados,
        public ?string $montoPromedio,
        public ?string $ultimaEntregaEn,
    ) {}
}
