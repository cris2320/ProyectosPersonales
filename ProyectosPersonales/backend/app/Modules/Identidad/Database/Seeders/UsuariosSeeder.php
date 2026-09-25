<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Database\Seeders;

use App\Modules\Identidad\Domain\Rol;
use App\Modules\Identidad\Infrastructure\Usuario;
use Illuminate\Database\Seeder;

/**
 * Usuarios iniciales SOLO para local/staging. En producción se crean desde el panel y se cambia la contraseña al primer acceso.
 */
final class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }
        $datos = [
            ['Cristhian Rodriguez Ruiz', 'admin', Rol::Administrador],
            ['Almacén 1', 'almacen1', Rol::Almacen],
            ['Almacén 2', 'almacen2', Rol::Almacen],
            ['Motorizado 1', 'moto1', Rol::Motorizado],
            ['Motorizado 2', 'moto2', Rol::Motorizado],
            ['Motorizado 3', 'moto3', Rol::Motorizado],
        ];
        foreach ($datos as [$nombre, $usuario, $rol]) {
            Usuario::query()->firstOrCreate(['usuario' => $usuario], [
                'nombre' => $nombre,
                'rol' => $rol,
                'activo' => true,
                'password_hash' => password_hash('cambiar123', PASSWORD_ARGON2ID),
            ]);
        }
    }
}
