<?php

declare(strict_types=1);

namespace App\Shared\Events;

interface Consumidor
{
    /** @param array<string, mixed> $evento Envoltura estándar (ver EventoDeDominio::aArray). */
    public function manejar(array $evento): void;
}
