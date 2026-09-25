<?php

declare(strict_types=1);

namespace App\Modules\Pedidos\Domain;

use DomainException;

final class TransicionInvalida extends DomainException
{
    public static function de(EstadoPedido $de, EstadoPedido $a): self
    {
        return new self(sprintf('No se puede pasar de %s a %s', $de->value, $a->value));
    }
}
