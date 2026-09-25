<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Infrastructure\Usuario;
use App\Shared\Audit\Auditoria;
use App\Shared\Http\ExcepcionDeDominio;
use PragmaRX\Google2FA\Google2FA;

/** Genera el secreto TOTP (paso 1) y lo confirma con un código válido (paso 2). */
final class ConfigurarMfa
{
    public function __construct(private readonly Google2FA $google2fa) {}

    /** @return array{secreto: string, otpauth_url: string} */
    public function generar(Usuario $u): array
    {
        $secreto = $this->google2fa->generateSecretKey(32);
        cache()->put("mfa:pendiente:{$u->uid}", $secreto, now()->addMinutes(10));

        return [
            'secreto' => $secreto,
            'otpauth_url' => $this->google2fa->getQRCodeUrl(config('app.name'), $u->usuario, $secreto),
        ];
    }

    public function confirmar(Usuario $u, string $codigo): void
    {
        $secreto = cache()->pull("mfa:pendiente:{$u->uid}");
        if ($secreto === null || ! $this->google2fa->verifyKey($secreto, $codigo, 1)) {
            throw new class extends ExcepcionDeDominio
            {
                public function __construct() { parent::__construct('Código incorrecto o expirado. Vuelve a generar el QR.'); }
                public function codigo(): string { return 'mfa_codigo_invalido'; }
                public function status(): int { return 422; }
            };
        }
        $u->establecerSecretoMfa($secreto);
        $u->save();
        $u->tokens()->where('abilities', 'like', '%mfa:configurar%')->delete();
        Auditoria::registrar('mfa.activado', 'Usuario', $u->uid, null, null, $u->uid);
    }
}
