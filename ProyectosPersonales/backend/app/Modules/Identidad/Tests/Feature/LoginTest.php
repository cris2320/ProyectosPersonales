<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Domain\Rol;
use App\Modules\Identidad\Infrastructure\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(Rol $rol, string $usuario = 'moto1'): Usuario
    {
        return Usuario::query()->create([
            'nombre' => 'Prueba', 'usuario' => $usuario, 'rol' => $rol, 'activo' => true,
            'password_hash' => password_hash('secreta', PASSWORD_ARGON2ID),
        ]);
    }

    public function test_motorizado_inicia_sesion_con_usuario_y_password(): void
    {
        $this->usuario(Rol::Motorizado);
        $this->postJson('/api/v1/auth/login', ['usuario' => 'moto1', 'password' => 'secreta'])
            ->assertOk()->assertJsonPath('usuario.rol', 'motorizado')->assertJsonStructure(['token']);
        $this->assertDatabaseHas('sys_auditoria', ['accion' => 'sesion.iniciada']);
    }

    public function test_password_incorrecta_devuelve_401_problem(): void
    {
        $this->usuario(Rol::Motorizado);
        $this->postJson('/api/v1/auth/login', ['usuario' => 'moto1', 'password' => 'mala'])
            ->assertStatus(401)->assertJsonPath('codigo', 'credenciales_invalidas');
    }

    public function test_administrador_sin_mfa_recibe_token_temporal_para_configurar(): void
    {
        $this->usuario(Rol::Administrador, 'admin');
        $r = $this->postJson('/api/v1/auth/login', ['usuario' => 'admin', 'password' => 'secreta'])
            ->assertStatus(401)->assertJsonPath('codigo', 'mfa_requerido')->assertJsonPath('debe_configurar_mfa', true);
        $this->assertNotEmpty($r->json('token_configuracion'));
    }

    public function test_administrador_con_mfa_necesita_codigo_valido(): void
    {
        $u = $this->usuario(Rol::Administrador, 'admin');
        $g = new Google2FA;
        $secreto = $g->generateSecretKey();
        $u->establecerSecretoMfa($secreto);
        $u->save();

        $this->postJson('/api/v1/auth/login', ['usuario' => 'admin', 'password' => 'secreta'])
            ->assertStatus(401)->assertJsonPath('debe_configurar_mfa', false);

        $this->postJson('/api/v1/auth/login', ['usuario' => 'admin', 'password' => 'secreta', 'codigo_mfa' => $g->getCurrentOtp($secreto)])
            ->assertOk()->assertJsonPath('usuario.rol', 'administrador');
    }

    public function test_ruta_protegida_por_rol(): void
    {
        $u = $this->usuario(Rol::Motorizado);
        $token = $u->createToken('t', ['rol:motorizado'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/auth/yo')->assertOk()->assertJsonPath('rol', 'motorizado');
    }
}
