<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Tests\Unit;

use App\Modules\Pedidos\Domain\EstadoPedido;
use App\Modules\Pedidos\Domain\TransicionInvalida;
use PHPUnit\Framework\TestCase;

final class EstadoPedidoTest extends TestCase
{
    public function test_recorrido_feliz_completo(): void
    {
        $ruta = [
            EstadoPedido::Creado, EstadoPedido::Confirmado, EstadoPedido::EnPreparacion,
            EstadoPedido::ListoParaDespacho, EstadoPedido::EnRuta, EstadoPedido::Entregado, EstadoPedido::Cobrado,
        ];
        for ($i = 0; $i < count($ruta) - 1; $i++) {
            $this->assertTrue($ruta[$i]->puedeTransicionarA($ruta[$i + 1]), "{$ruta[$i]->value} -> {$ruta[$i + 1]->value}");
        }
        $this->assertTrue(EstadoPedido::Cobrado->esFinal());
    }

    public function test_un_pedido_entregado_no_puede_cancelarse(): void
    {
        $this->expectException(TransicionInvalida::class);
        EstadoPedido::Entregado->transicionarA(EstadoPedido::Cancelado);
    }

    public function test_entrega_fallida_permite_reintento_o_devolucion(): void
    {
        $this->assertTrue(EstadoPedido::EntregaFallida->puedeTransicionarA(EstadoPedido::ListoParaDespacho));
        $this->assertTrue(EstadoPedido::EntregaFallida->puedeTransicionarA(EstadoPedido::Devuelto));
        $this->assertFalse(EstadoPedido::EntregaFallida->puedeTransicionarA(EstadoPedido::Entregado));
    }
}
