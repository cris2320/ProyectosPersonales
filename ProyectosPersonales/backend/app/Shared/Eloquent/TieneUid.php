<?php

declare(strict_types=1);

namespace App\Shared\Eloquent;

use Symfony\Component\Uid\Ulid;

/**
 * Genera el identificador público `uid` (ULID) al crear (ADR-003).
 * Uso: `use TieneUid;` en el modelo. Las rutas resuelven por uid: getRouteKeyName().
 */
trait TieneUid
{
    public static function bootTieneUid(): void
    {
        static::creating(function ($modelo): void {
            if (empty($modelo->uid)) {
                $modelo->uid = (string) new Ulid;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uid';
    }
}
