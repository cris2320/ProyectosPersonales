<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain;

/** Roles del personal (02 §4.9). */
enum Rol: string
{
    case Administrador = 'administrador';
    case Almacen = 'almacen';
    case Motorizado = 'motorizado';

    public function requiereMfa(): bool
    {
        return $this === self::Administrador;
    }

    public function etiqueta(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Almacen => 'Almacén',
            self::Motorizado => 'Motorizado',
        };
    }
}
