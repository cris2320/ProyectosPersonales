<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

final readonly class ResultadoSesion
{
    public function __construct(
        public string $token,
        public string $uid,
        public string $nombre,
        public string $rol,
        public bool $mfaActivo,
    ) {}
}
