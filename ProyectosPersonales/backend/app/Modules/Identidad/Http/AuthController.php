<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http;

use App\Modules\Identidad\Application\ConfigurarMfa;
use App\Modules\Identidad\Application\IniciarSesion;
use App\Modules\Identidad\Http\Requests\LoginRequest;
use App\Modules\Identidad\Infrastructure\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** MVC: valida → caso de uso → respuesta. Sin reglas de negocio aquí (ADR-005). */
final class AuthController
{
    public function login(LoginRequest $request, IniciarSesion $iniciarSesion): JsonResponse
    {
        $r = $iniciarSesion->ejecutar(
            $request->string('usuario')->toString(),
            $request->string('password')->toString(),
            $request->input('codigo_mfa'),
            $request->input('dispositivo', 'web'),
        );

        return response()->json([
            'token' => $r->token,
            'usuario' => ['uid' => $r->uid, 'nombre' => $r->nombre, 'rol' => $r->rol, 'mfa_activo' => $r->mfaActivo],
        ]);
    }

    public function yo(Request $request): JsonResponse
    {
        /** @var Usuario $u */
        $u = $request->user('personal');

        return response()->json(['uid' => $u->uid, 'nombre' => $u->nombre, 'usuario' => $u->usuario, 'rol' => $u->rol->value, 'mfa_activo' => $u->tieneMfa()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user('personal')?->currentAccessToken()?->delete();

        return response()->json(null, 204);
    }

    public function generarMfa(Request $request, ConfigurarMfa $configurar): JsonResponse
    {
        return response()->json($configurar->generar($request->user('personal')));
    }

    public function confirmarMfa(Request $request, ConfigurarMfa $configurar): JsonResponse
    {
        $request->validate(['codigo' => ['required', 'digits:6']]);
        $configurar->confirmar($request->user('personal'), $request->string('codigo')->toString());

        return response()->json(['mfa_activo' => true]);
    }
}
