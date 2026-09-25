<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Contracts;

/**
 * API pública del módulo Pedidos. Es lo ÚNICO que otros módulos pueden usar (ADR-005).
 * Implementada en Infrastructure y enlazada en PedidosServiceProvider.
 */
interface PedidosApi
{
    /** Resumen de un pedido para otros módulos (Fulfillment, Cobranza, Riesgo). */
    public function obtenerResumen(string $pedidoUid): ?PedidoResumen;

    /** Historial resumido del cliente para Riesgo: entregados, rechazados, monto promedio. */
    public function historialCliente(string $clienteUid): HistorialCliente;
}
