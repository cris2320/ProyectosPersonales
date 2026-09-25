<?php

declare(strict_types=1);

namespace App\Shared\Money;

use InvalidArgumentException;

/**
 * Value object de dinero en PEN. Aritmética exacta con bcmath; nunca float (ADR-003).
 * Se serializa como "86.00" en la API (esquema Dinero del contrato).
 */
final readonly class Dinero
{
    private function __construct(public string $monto, public string $moneda = 'PEN')
    {
        if (! preg_match('/^\d+\.\d{2}$/', $monto)) {
            throw new InvalidArgumentException("Monto inválido: {$monto}");
        }
    }

    public static function de(string $valor): self
    {
        return new self(bcadd($valor, '0', 2));
    }

    public static function cero(): self
    {
        return new self('0.00');
    }

    public function mas(self $otro): self
    {
        return new self(bcadd($this->monto, $otro->monto, 2));
    }

    public function menos(self $otro): self
    {
        return new self(bcsub($this->monto, $otro->monto, 2));
    }

    public function por(int $cantidad): self
    {
        return new self(bcmul($this->monto, (string) $cantidad, 2));
    }

    public function esMayorOIgualQue(self $otro): bool
    {
        return bccomp($this->monto, $otro->monto, 2) >= 0;
    }

    /** Texto para pantalla: "S/ 1 234,50" (05 §6). */
    public function formateado(): string
    {
        [$entero, $dec] = explode('.', $this->monto);

        return 'S/ '.number_format((int) $entero, 0, '', ' ').','.$dec;
    }

    public function __toString(): string
    {
        return $this->monto;
    }
}
