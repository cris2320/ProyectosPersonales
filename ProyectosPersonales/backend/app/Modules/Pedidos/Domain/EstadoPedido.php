<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Domain;

/**
 * Máquina de estados del pedido (02 §4.5). PHP puro, sin Laravel.
 * Solo estas transiciones son válidas; cualquier otra lanza TransicionInvalida.
 */
enum EstadoPedido: string
{
    case Creado = 'Creado';
    case PendienteDeRevision = 'PendienteDeRevision';
    case Confirmado = 'Confirmado';
    case EnPreparacion = 'EnPreparacion';
    case ListoParaDespacho = 'ListoParaDespacho';
    case EnRuta = 'EnRuta';
    case Entregado = 'Entregado';
    case Cobrado = 'Cobrado';
    case EntregaFallida = 'EntregaFallida';
    case Devuelto = 'Devuelto';
    case Cancelado = 'Cancelado';

    /** @return list<self> */
    public function transicionesPermitidas(): array
    {
        return match ($this) {
            self::Creado => [self::PendienteDeRevision, self::Confirmado, self::Cancelado],
            self::PendienteDeRevision => [self::Confirmado, self::Cancelado],
            self::Confirmado => [self::EnPreparacion, self::Cancelado],
            self::EnPreparacion => [self::ListoParaDespacho],
            self::ListoParaDespacho => [self::EnRuta],
            self::EnRuta => [self::Entregado, self::EntregaFallida],
            self::Entregado => [self::Cobrado],
            self::EntregaFallida => [self::ListoParaDespacho, self::Devuelto],
            self::Cobrado, self::Devuelto, self::Cancelado => [],
        };
    }

    public function puedeTransicionarA(self $destino): bool
    {
        return in_array($destino, $this->transicionesPermitidas(), true);
    }

    public function transicionarA(self $destino): self
    {
        if (! $this->puedeTransicionarA($destino)) {
            throw TransicionInvalida::de($this, $destino);
        }

        return $destino;
    }

    public function esFinal(): bool
    {
        return $this->transicionesPermitidas() === [];
    }

    /** Texto para el cliente (05 §3.8). */
    public function textoCliente(): string
    {
        return match ($this) {
            self::Creado, self::PendienteDeRevision => 'Recibido · Lo estamos revisando',
            self::Confirmado, self::EnPreparacion, self::ListoParaDespacho => 'Confirmado · Preparando tu pedido',
            self::EnRuta => 'En camino',
            self::Entregado, self::Cobrado => 'Entregado',
            self::EntregaFallida => 'No pudimos entregarlo · Te contactaremos para reprogramar',
            self::Cancelado => 'Cancelado',
            self::Devuelto => 'Devuelto al almacén',
        };
    }
}
