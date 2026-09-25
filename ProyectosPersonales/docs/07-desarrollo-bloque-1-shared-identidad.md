# 07 · Desarrollo · Bloque 1: Shared e Identidad

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Estado | Entregado para integración — se cierra cuando las pruebas pasen en CI |
| Fecha | 2026-09-14 |
| Depende de | 06 (entorno listo) · ADR-002, 005, 006 · 04 §9, §11 |
| Para | Desarrollador backend (integración) · Desarrollador frontend (§5) · Dueño (§6) |

---

## 1. Qué contiene este bloque

### Shared (`backend/app/Shared/`) — infraestructura común

| Archivo | Qué hace | Referencia |
|---|---|---|
| `Events/EventoDeDominio.php` | Clase base de todo evento: `event_id` ULID, nombre, versión, agregado, `payload()` | ADR-006 |
| `Events/PublicadorEventos.php` | Inserta el evento en `sys_outbox` **dentro** de la transacción; falla si no hay transacción abierta | ADR-006 |
| `Console/DespacharOutbox.php` | Worker `outbox:despachar [--loop]`: toma lotes, los envía a la cola `eventos`, marca despachados, cuenta intentos | 04 §11 |
| `Events/EventoPublicado.php` | Job que viaja por Redis y entrega el evento a los consumidores registrados; reintentos con espera creciente | ADR-006 |
| `Events/RegistroConsumidores.php` + `Consumidor.php` | Cada módulo registra `nombre.vN → clase consumidora` en su ServiceProvider | ADR-006 |
| `Events/ConsumidorIdempotente.php` | Trait: `$this->unaVez($evento, fn () => ...)` garantiza procesar cada `event_id` una sola vez (`sys_eventos_procesados`) | 04 §11 |
| `Idempotency/IdempotencyKey.php` | Middleware `idempotente`: exige `Idempotency-Key`, cachea la respuesta 24 h, detecta reuso con cuerpo distinto | 03 §8.4 |
| `Http/ProblemDetails.php` | Respuestas de error RFC 9457 con `codigo` estable | 03 §8.3 |
| `Http/ExcepcionDeDominio.php` | Base de los errores de negocio (`StockInsuficiente`, `CupoAgotado`…) con código y status | 03 §8.3 |
| `Http/ManejadorDeExcepciones.php` | Convierte toda excepción de `/api/*` a Problem Details (validación, auth, 404, dominio, 500) | 03 §8.3 |
| `Http/TraceId.php` | Middleware: propaga `X-Trace-Id` y lo mete en el contexto de logs | 03 §8.5 |
| `Audit/Auditoria.php` | `Auditoria::registrar(...)` → `sys_auditoria` (sin datos personales en claro) | 04 §11 |
| `Config/Configuracion.php` | Lectura/escritura cacheada de `sys_configuracion` con auditoría | 04 §11 |
| `Eloquent/TieneUid.php` | Trait: genera `uid` ULID al crear y resuelve rutas por `uid` | ADR-003 |
| `Money/Dinero.php` | Value object exacto (bcmath), formato `S/ 1 234,50` | ADR-003 |
| `Console/MakeModule.php` | `php artisan make:module Nombre` genera la estructura del ADR-005 | ADR-005 |
| `Database/Migrations/…create_sys_tables.php` | `sys_outbox`, `sys_eventos_procesados`, `sys_idempotencia`, `sys_auditoria`, `sys_configuracion` | 04 §11 |
| `Database/Seeders/ConfiguracionSeeder.php` | Valores iniciales confirmados (manual para todos = sí, umbral 3, 30 min, 2 h, 1 reintento, 30 por moto, 5 unidades, horario 7–21) | 02 §9 |
| `Tests/` | Outbox (dentro/fuera de transacción, rollback, despacho), Idempotency-Key (3 casos), Dinero | 03 §8.8 |

### Identidad (`backend/app/Modules/Identidad/`) — login del personal

| Archivo | Qué hace |
|---|---|
| `Domain/Rol.php` | `administrador` / `almacen` / `motorizado`; el administrador exige MFA (01 §6.4) |
| `Domain/CredencialesInvalidas.php`, `MfaRequerido.php` | Errores de dominio con código estable (401) |
| `Infrastructure/Usuario.php` | Modelo `idn_usuarios`, guard `personal`, Argon2id, secreto MFA cifrado |
| `Application/IniciarSesion.php` | Login por **usuario + contraseña** (confirmado por el dueño); si el rol exige MFA y no está configurado, devuelve un token temporal de 15 min solo para configurarlo; si está configurado, exige el código de 6 dígitos. Token de sesión de 12 h con habilidad `rol:<rol>`. Audita éxitos y fallos |
| `Application/ConfigurarMfa.php` | Genera secreto TOTP + URL para QR (10 min) y lo confirma con un código válido |
| `Http/AuthController.php`, `routes.php` | `POST /auth/login` (10 intentos/min), `GET /auth/yo`, `POST /auth/logout`, `POST /auth/mfa/generar`, `POST /auth/mfa/confirmar` |
| `Http/RequiereRol.php` | Middleware `rol:administrador,almacen` sobre la habilidad del token |
| `Contracts/IdentidadApi.php` | Lo que otros módulos pueden pedir: `obtener(uid)`, `motorizadosActivos()` |
| `Database/` | Migración `idn_usuarios` (04 §9) y seeder con admin, 2 almacén, 3 motorizados (**solo local/staging**, contraseña `cambiar123`) |
| `Tests/Feature/LoginTest.php` | 5 casos: login motorizado, contraseña mala, admin sin MFA, admin con MFA, ruta protegida |

El contrato `contracts/openapi.yaml` pasa a **v0.2.0** con los cinco endpoints de `auth`, el esquema `bearer` y los códigos de error nuevos.

## 2. Integración en el proyecto Laravel (backend, ~20 min)

1. **Copiar** `backend/app/Shared/` y `backend/app/Modules/Identidad/` al proyecto (sobre lo generado en el paso 6).
2. **Paquete adicional:**
   ```bash
   art composer require pragmarx/google2fa
   ```
   (`symfony/uid`, usado para ULID, ya viene con Laravel.)
3. **`bootstrap/providers.php`:** añadir
   ```php
   App\Shared\SharedServiceProvider::class,
   App\Modules\Identidad\IdentidadServiceProvider::class,
   App\Modules\Pedidos\PedidosServiceProvider::class, // cuando exista
   ```
4. **`bootstrap/app.php`:**
   ```php
   ->withMiddleware(function (Middleware $middleware): void {
       $middleware->api(prepend: [\App\Shared\Http\TraceId::class]);
       $middleware->alias([
           'idempotente' => \App\Shared\Idempotency\IdempotencyKey::class,
           'rol' => \App\Modules\Identidad\Http\RequiereRol::class,
           'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
           'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
       ]);
   })
   ->withExceptions(function (Exceptions $exceptions): void {
       \App\Shared\Http\ManejadorDeExcepciones::registrar($exceptions);
   })
   ```
5. **`config/sanctum.php`:** `'expiration' => null` (la expiración la fija cada token al crearse) y `'guard' => ['personal', 'web']`.
6. **`database/seeders/DatabaseSeeder.php`:** llamar a `\App\Shared\Database\Seeders\ConfiguracionSeeder::class` y `\App\Modules\Identidad\Database\Seeders\UsuariosSeeder::class`.
7. **`phpunit.xml`:** testsuites con `app/Shared/Tests` y `app/Modules/*/Tests`. El `.env.ci` usa MySQL real, como exige el 03 §8.8.
8. **`infra/docker-compose.yml`:** añadir el worker del outbox como servicio (o dejarlo al scheduler cada minuto, que ya está):
   ```yaml
   outbox:
     build: { context: .., dockerfile: infra/docker/php/Dockerfile }
     command: php artisan outbox:despachar --loop
     env_file: [../backend/.env]
     volumes: ['../backend:/var/www/html']
     depends_on: [api]
   ```
   En `config/horizon.php`, incluir la cola `eventos` en el supervisor.
9. **Verificar:**
   ```bash
   art php artisan migrate:fresh --seed
   art php artisan test            # Outbox, IdempotencyKey, Dinero, EstadoPedido, Login en verde
   art vendor/bin/deptrac analyse  # sin violaciones
   curl -X POST localhost:8080/api/v1/auth/login -H 'Content-Type: application/json' -d '{"usuario":"moto1","password":"cambiar123"}'
   ```

**Nota sobre `SKIP LOCKED`:** `DespacharOutbox` usa `lockForUpdate()`. Con un solo worker basta. Al añadir un segundo worker, cambiar por `->lock('FOR UPDATE SKIP LOCKED')` (MySQL 8) para que no se bloqueen entre sí, como indica el comentario en el código.

## 3. Cómo usar Shared desde los próximos módulos (patrón)

```php
// 1) Evento del módulo (Events/PedidoCreado.php)
final class PedidoCreado extends EventoDeDominio {
    public static function nombre(): string { return 'PedidoCreado'; }
    public static function version(): int { return 1; }
    public static function agregadoTipo(): string { return 'Pedido'; }
    public function payload(): array { return $this->datos; } // debe cumplir contracts/events/PedidoCreado.v1.json
}

// 2) Caso de uso: todo dentro de UNA transacción
DB::transaction(function () {
    $inventario->reservar(...);          // Contracts de otro módulo
    $zonas->tomarCupo(...);
    $pedido = Pedido::create([...]);
    $this->publicador->publicar(new PedidoCreado($pedido->uid, $datos));
});

// 3) Consumidor en otro módulo (Riesgo/Application/EvaluarPedido.php)
final class EvaluarPedido implements Consumidor {
    use ConsumidorIdempotente;
    public function manejar(array $evento): void {
        $this->unaVez($evento, fn () => $this->evaluar($evento['payload']));
    }
}
// y en RiesgoServiceProvider::boot: $registro->registrar('PedidoCreado', 1, EvaluarPedido::class);

// 4) Error de negocio hacia el cliente
final class StockInsuficiente extends ExcepcionDeDominio {
    public function __construct(array $skus) { parent::__construct('No hay stock suficiente.'); $this->extra = ['skus' => $skus]; }
    public function codigo(): string { return 'stock_insuficiente'; }
}
// → 409 application/problem+json {"codigo":"stock_insuficiente","skus":[...]}
```

## 4. Decisiones tomadas al codificar (menores, sin ADR)

- Tokens Sanctum con **expiración por token** (12 h sesión, 15 min configuración MFA) en vez de global.
- El middleware de idempotencia agrupa a los invitados por **IP hasheada**; cuando exista sesión de cliente de tienda, se usará su `uid`.
- `sys_idempotencia.actor_uid` se amplió a `VARCHAR(80)` para admitir el prefijo `anon:`.
- La consulta de configuración se cachea **60 s**: un cambio en el panel tarda hasta un minuto en aplicarse. Aceptable; si molesta, `Cache::forget` ya se llama al escribir.
- Los seeders de usuarios **no corren en producción**; ahí el primer administrador se crea con un comando artisan que se escribirá en el bloque del panel.

## 5. Frontend: qué puede empezar ya

Con el contrato v0.2.0 y `npm run api:generate`:
- Pantalla de login del **panel** y de la **app del motorizado** (usuario + contraseña; si la respuesta es `mfa_requerido` con `debe_configurar_mfa: true`, flujo de configuración con QR desde `otpauth_url`; si es `false`, pedir el código de 6 dígitos).
- Guardar el token en memoria y en `IndexedDB` (motorizado) / `sessionStorage` (panel); interceptor que añade `Authorization: Bearer` y `X-Trace-Id`; interceptor que traduce `application/problem+json` a mensajes por `codigo`.
- Guard de rutas por `rol`.
- Mock mientras el backend integra: `npx @stoplight/prism-cli mock ../contracts/openapi.yaml`.

## 6. Para el dueño

Lo que este bloque significa en tu operación: cuando el panel exista, tú entrarás con **usuario y contraseña más un código de 6 dígitos** de una app gratuita como Google Authenticator o Microsoft Authenticator en tu celular; almacén y motorizados entran solo con usuario y contraseña (pueden activar el código si quieren). Toda entrada, fallida o exitosa, queda registrada. Y ninguna acción sobre pedidos, cobros o precios podrá ocurrir sin quedar en la auditoría, porque la base para eso queda instalada aquí.

## 7. Siguiente bloque (2): Catálogo + Inventario

Contrato: categorías, productos, presentaciones, precios por lista, disponibilidad; comandos de inventario (entrada, ajuste), reservas con bloqueo de fila y prueba de concurrencia; evento `StockReservado` / `ReservaLiberada` / `StockAgotado` / `StockReingresado`; canal `catalogo.disponibilidad` en Reverb. Frontend: tienda navegable con badge en tiempo real y panel de catálogo/inventario.
