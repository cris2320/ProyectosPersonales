<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Contracts;

/** API pública de Identidad para otros módulos (Fulfillment, Cobranza, Catálogo). */
interface IdentidadApi
{
    public function obtener(string $uid): ?UsuarioResumen;

    /** @return list<UsuarioResumen> */
    public function motorizadosActivos(): array;
}
