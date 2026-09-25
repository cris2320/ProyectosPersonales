<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain;

use App\Shared\Http\ExcepcionDeDominio;

final class CredencialesInvalidas extends ExcepcionDeDominio
{
    public function __construct()
    {
        parent::__construct('Usuario o contraseña incorrectos.');
    }

    public function codigo(): string
    {
        return 'credenciales_invalidas';
    }

    public function status(): int
    {
        return 401;
    }
}
