<?php

declare(strict_types=1);

namespace App\Shared\Tests\Unit;

use App\Shared\Money\Dinero;
use PHPUnit\Framework\TestCase;

final class DineroTest extends TestCase
{
    public function test_suma_y_multiplicacion_exactas(): void
    {
        $total = Dinero::de('12.90')->por(3)->mas(Dinero::de('44.90'));
        $this->assertSame('83.60', (string) $total);
    }

    public function test_vuelto(): void
    {
        $this->assertSame('14.00', (string) Dinero::de('100.00')->menos(Dinero::de('86.00')));
        $this->assertTrue(Dinero::de('100.00')->esMayorOIgualQue(Dinero::de('86.00')));
    }

    public function test_formato_peruano(): void
    {
        $this->assertSame('S/ 1 234,50', Dinero::de('1234.5')->formateado());
    }
}
