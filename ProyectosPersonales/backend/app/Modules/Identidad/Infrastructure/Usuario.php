<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure;

use App\Modules\Identidad\Domain\Rol;
use App\Shared\Eloquent\TieneUid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

/**
 * Tabla idn_usuarios (04 §9). Guard `personal`. Login por `usuario` + contraseña (confirmado por el dueño).
 *
 * @property string $uid
 * @property string $nombre
 * @property string $usuario
 * @property ?string $correo
 * @property Rol $rol
 * @property bool $activo
 * @property ?string $mfa_secreto_cifrado
 */
final class Usuario extends Authenticatable
{
    use HasApiTokens, TieneUid;

    protected $table = 'idn_usuarios';

    public const CREATED_AT = 'creado_en';

    public const UPDATED_AT = 'actualizado_en';

    protected $fillable = ['nombre', 'usuario', 'correo', 'telefono', 'password_hash', 'rol', 'activo'];

    protected $hidden = ['password_hash', 'mfa_secreto_cifrado'];

    protected $casts = ['rol' => Rol::class, 'activo' => 'boolean', 'ultimo_acceso_en' => 'datetime'];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function tieneMfa(): bool
    {
        return $this->mfa_secreto_cifrado !== null;
    }

    public function secretoMfa(): ?string
    {
        return $this->mfa_secreto_cifrado === null ? null : Crypt::decryptString($this->mfa_secreto_cifrado);
    }

    public function establecerSecretoMfa(?string $secreto): void
    {
        $this->mfa_secreto_cifrado = $secreto === null ? null : Crypt::encryptString($secreto);
    }

    protected function password(): Attribute
    {
        return Attribute::make(set: fn (string $v) => ['password_hash' => password_hash($v, PASSWORD_ARGON2ID)]);
    }
}
