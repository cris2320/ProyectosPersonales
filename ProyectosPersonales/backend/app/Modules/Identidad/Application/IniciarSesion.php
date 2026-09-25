<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Domain\CredencialesInvalidas;
use App\Modules\Identidad\Domain\MfaRequerido;
use App\Modules\Identidad\Infrastructure\Usuario;
use App\Shared\Audit\Auditoria;
use PragmaRX\Google2FA\Google2FA;

/**
 * Caso de uso: login del personal con usuario + contraseña (+ código TOTP si el rol lo exige o el usuario lo activó).
 * Devuelve un token Sanctum con habilidades según rol. Registra auditoría de accesos.
 */
final class IniciarSesion
{
    public function __construct(private readonly Google2FA $google2fa) {}

    public function ejecutar(string $usuario, string $password, ?string $codigoMfa, string $dispositivo): ResultadoSesion
    {
        $u = Usuario::query()->where('usuario', mb_strtolower(trim($usuario)))->first();

        if ($u === null || ! $u->activo || ! password_verify($password, $u->password_hash)) {
            Auditoria::registrar('sesion.fallida', 'Usuario', $u?->uid ?? 'desconocido', null, ['usuario' => mb_substr($usuario, 0, 40)], null, 'sistema');
            throw new CredencialesInvalidas;
        }

        $exigeMfa = $u->rol->requiereMfa() || $u->tieneMfa();
        if ($exigeMfa) {
            if (! $u->tieneMfa()) {
                // Administrador sin MFA configurado: token temporal solo para configurar MFA.
                $token = $u->createToken($dispositivo, ['mfa:configurar'], now()->addMinutes(15))->plainTextToken;
                throw (new MfaRequerido(true))->conToken($token);
            }
            if ($codigoMfa === null || ! $this->google2fa->verifyKey((string) $u->secretoMfa(), $codigoMfa, 1)) {
                throw new MfaRequerido(false);
            }
        }

        $u->forceFill(['ultimo_acceso_en' => now()])->save();
        $token = $u->createToken($dispositivo, ['rol:'.$u->rol->value], now()->addHours(12))->plainTextToken;
        Auditoria::registrar('sesion.iniciada', 'Usuario', $u->uid, null, ['dispositivo' => $dispositivo], $u->uid);

        return new ResultadoSesion($token, $u->uid, $u->nombre, $u->rol->value, $u->tieneMfa());
    }
}
