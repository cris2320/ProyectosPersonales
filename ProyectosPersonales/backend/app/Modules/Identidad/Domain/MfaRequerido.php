<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain;

use App\Shared\Http\ExcepcionDeDominio;

/** El usuario debe enviar el código TOTP, o configurar MFA si su rol lo exige y aún no lo tiene. */
final class MfaRequerido extends ExcepcionDeDominio
{
    public function __construct(bool $debeConfigurar)
    {
        parent::__construct($debeConfigurar
            ? 'Tu rol exige verificación en dos pasos. Configúrala para continuar.'
            : 'Ingresa el código de tu aplicación de autenticación.');
        $this->extra = ['debe_configurar_mfa' => $debeConfigurar];
    }

    /** Token temporal con habilidad mfa:configurar (15 min). */
    public function conToken(string $token): self
    {
        $this->extra['token_configuracion'] = $token;

        return $this;
    }

    public function codigo(): string
    {
        return 'mfa_requerido';
    }

    public function status(): int
    {
        return 401;
    }
}
