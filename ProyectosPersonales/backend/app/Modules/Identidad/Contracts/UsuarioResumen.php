<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Contracts;

final readonly class UsuarioResumen
{
    public function __construct(
        public string $uid,
        public string $nombre,
        public string $usuario,
        public string $rol,
        public bool $activo,
    ) {}
}
