<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure;

use App\Modules\Identidad\Contracts\IdentidadApi;
use App\Modules\Identidad\Contracts\UsuarioResumen;
use App\Modules\Identidad\Domain\Rol;

final class IdentidadApiEloquent implements IdentidadApi
{
    public function obtener(string $uid): ?UsuarioResumen
    {
        $u = Usuario::query()->where('uid', $uid)->first();

        return $u === null ? null : $this->resumen($u);
    }

    public function motorizadosActivos(): array
    {
        return Usuario::query()->where('rol', Rol::Motorizado->value)->where('activo', true)->orderBy('nombre')
            ->get()->map(fn (Usuario $u) => $this->resumen($u))->all();
    }

    private function resumen(Usuario $u): UsuarioResumen
    {
        return new UsuarioResumen($u->uid, $u->nombre, $u->usuario, $u->rol->value, $u->activo);
    }
}
