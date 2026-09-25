<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Contracts;

final readonly class PedidoResumen
{
    public function __construct(
        public string $uid,
        public string $numero,
        public string $estado,
        public string $clienteUid,
        public string $clienteTelefono,
        public string $total,        // "86.00"
        public ?string $pagaCon,     // "100.00" | null
        public string $metodoPago,   // efectivo|yape|plin|tarjeta
        public string $fechaEntrega, // Y-m-d
        public string $franjaUid,
        public float $lat,
        public float $lng,
    ) {}
}
