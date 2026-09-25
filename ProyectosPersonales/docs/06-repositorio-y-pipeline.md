# 06 · Repositorio y pipeline: guía de arranque

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Estado | Borrador v0.1 — se cierra cuando ambos desarrolladores tengan el entorno corriendo |
| Fecha | 2026-09-14 |
| Depende de | ADR-001 a ADR-006 · 03 §5, §7 · 04 §15 |
| Para | Desarrollador backend (§3), desarrollador frontend (§4), dueño (§2) |

> Este documento acompaña al paquete `dtoo-limpieza/` que contiene la estructura del repositorio, la configuración de Docker, el CI de GitHub Actions, el contrato OpenAPI inicial, la verificación de fronteras entre módulos y el primer código del dominio. Lo que **no** contiene es el esqueleto generado por los instaladores de Laravel y Angular: eso lo crea cada desarrollador con los comandos de §3 y §4 (toma unos 30 minutos por lado).

---

## 1. Qué hay en el paquete

```
dtoo-limpieza/
├── README.md                     arranque rápido y reglas
├── Makefile                      make up / migrate / test / lint / contract
├── .editorconfig .gitignore .gitattributes
├── .github/
│   ├── workflows/ci.yml          pipeline completo (contratos, backend, frontend, secretos)
│   ├── workflows/deploy-staging.yml   esqueleto; se completa con ADR-009
│   ├── PULL_REQUEST_TEMPLATE.md  checklist del PR
│   ├── CODEOWNERS                revisión cruzada
│   └── dependabot.yml
├── docs/                         01–05 y adr/ (docs as code)
├── contracts/
│   ├── openapi.yaml              contrato inicial: /salud, /catalogo/categorias, POST /pedidos
│   └── events/PedidoCreado.v1.json
├── infra/
│   ├── docker-compose.yml        mysql, redis, api, nginx, queue, scheduler, reverb, frontend, mailpit
│   ├── .env.example
│   └── docker/{php,nginx,node}/
├── backend/
│   ├── deptrac.yaml              fronteras entre módulos (ADR-005)
│   ├── phpstan.neon  pint.json  .env.ci  .env.example.dtoo
│   └── app/
│       ├── Shared/Money/Dinero.php               value object de dinero (listo)
│       └── Modules/Pedidos/
│           ├── Contracts/                        API pública del módulo
│           ├── Domain/EstadoPedido.php           máquina de estados (lista)
│           ├── Domain/TransicionInvalida.php
│           └── Tests/Unit/EstadoPedidoTest.php   primera prueba del proyecto
└── frontend/
    ├── openapi-ts.config.ts      generación del cliente desde el contrato
    ├── lighthouserc.json         presupuesto de rendimiento
    └── libs/design-system/tokens.css   paleta azul/amarillo (05 §7)
```

## 2. Dueño: crear el repositorio en GitHub (15 min)

1. Crear repositorio privado `dtoo-limpieza` en la organización o cuenta de GitHub.
2. Subir el contenido del paquete como primer commit: `chore: estructura inicial del repositorio y documentación`.
3. **Settings → Branches → Add rule** para `main`:
   - Require a pull request before merging → Require approvals: **1**.
   - Require review from Code Owners.
   - Require status checks to pass: seleccionar `contracts`, `backend`, `frontend`, `secretos` (aparecen tras el primer CI).
   - Require conversation resolution before merging.
   - Do not allow bypassing the above settings.
4. **Settings → Environments**: crear `staging` y `production`; en `production` activar *Required reviewers* con tu usuario (es el "clic de promoción" del ADR-004).
5. Editar `.github/CODEOWNERS` con los tres usuarios reales de GitHub.
6. Invitar a los dos desarrolladores con rol *Write* (no *Admin*).
7. **Settings → Code security**: activar Dependabot alerts y Secret scanning.

## 3. Desarrollador backend: esqueleto Laravel (30 min)

Requisitos: Docker Desktop, Git. PHP y Composer locales son opcionales porque todo corre en contenedores.

```bash
git clone <repo> && cd dtoo-limpieza
cp infra/.env.example infra/.env

# 3.1 Crear el proyecto Laravel DENTRO de backend/ usando el contenedor (no hace falta PHP local)
docker run --rm -v "$PWD/backend":/app -w /app composer:2 \
  create-project laravel/laravel tmp --no-interaction --prefer-dist
# mover el contenido de tmp/ a backend/ sin pisar los archivos del paquete
rsync -a --ignore-existing backend/tmp/ backend/ && rm -rf backend/tmp

# 3.2 Configuración del entorno
cp backend/.env.example backend/.env
cat backend/.env.example.dtoo >> backend/.env      # añade las variables del proyecto (MySQL, Redis, Reverb, Mailpit)

# 3.3 Levantar la pila y generar la clave
make up
docker compose -f infra/docker-compose.yml --env-file infra/.env exec api php artisan key:generate
```

**3.4 Paquetes** (dentro del contenedor `api`):

```bash
alias art='docker compose -f infra/docker-compose.yml --env-file infra/.env exec api'
art composer require laravel/sanctum laravel/horizon laravel/reverb
art composer require --dev larastan/larastan qossmic/deptrac laravel/pint pestphp/pest pestphp/pest-plugin-laravel
art php artisan install:api            # Sanctum + routes/api.php
art php artisan horizon:install
art php artisan reverb:install
```

**3.5 Registro de módulos.** En `composer.json`, asegurarse de que `App\\` apunta a `app/` (ya viene así): los namespaces `App\Modules\Pedidos\...` y `App\Shared\...` se autocargan sin más. Registrar cada módulo en `bootstrap/providers.php` a medida que se cree (`App\Modules\Pedidos\PedidosServiceProvider::class`).

**3.6 Ajustes obligatorios de configuración:**
- `config/app.php`: `'timezone' => 'UTC'`, `'locale' => 'es'`.
- `config/database.php` (mysql): `'charset' => 'utf8mb4'`, `'collation' => 'utf8mb4_0900_ai_ci'`, `'strict' => true`.
- `config/sanctum.php`: `stateful` con los tres orígenes de los frontends.
- `config/cors.php`: `allowed_origins` = `http://localhost:4200`, `:4201`, `:4202`.
- `phpunit.xml`: añadir `<directory>app/Modules/*/Tests</directory>` a los testsuites para que las pruebas de cada módulo se ejecuten.

**3.7 Verificar:**
```bash
art vendor/bin/pint --test
art vendor/bin/phpstan analyse
art vendor/bin/deptrac analyse           # debe pasar: solo existe Pedidos por ahora
art php artisan test                     # EstadoPedidoTest en verde
curl http://localhost:8080/up            # {"status":"ok"} (ruta de salud de Laravel)
```

**3.8 Primer PR del backend:** `feat(pedidos): esqueleto de módulos, dominio de estados y configuración de calidad`. Debe incluir la ruta `GET /api/v1/salud` que devuelve `{estado: "ok", version}` según el contrato, y el comando `openapi:verify` (o un paso equivalente en CI con `vendor/bin/openapi` de `zircote/swagger-php` si se prefiere generar el contrato desde anotaciones; **la decisión se anota en el PR**).

## 4. Desarrollador frontend: workspace Angular (30 min)

Requisitos: Node 22 LTS, npm 10. Puede hacerse en el host o dentro del contenedor `frontend`.

```bash
cd frontend
# 4.1 Workspace vacío + tres aplicaciones
npx @angular/cli@latest new dtoo --create-application=false --directory=. --package-manager=npm --style=scss --skip-git
npx ng generate application tienda     --style=scss --routing --ssr --standalone --prefix=dt
npx ng generate application panel      --style=scss --routing --standalone --prefix=dt
npx ng generate application motorizado --style=scss --routing --standalone --prefix=dt

# 4.2 Librerías
for l in design-system api-client dominio realtime offline-queue; do npx ng generate library $l --prefix=dt --standalone; done

# 4.3 PWA en panel y motorizado; Angular Material como base del design system
npx ng add @angular/pwa --project=panel
npx ng add @angular/pwa --project=motorizado
npx ng add @angular/material --project=tienda   # elegir tema custom; los tokens vienen de libs/design-system/tokens.css

# 4.4 Calidad y generación del cliente
npx ng add @angular-eslint/schematics
npm i -D @hey-api/openapi-ts @hey-api/client-fetch prettier @lhci/cli
npm pkg set scripts.api:generate="openapi-ts -f openapi-ts.config.ts"
npm pkg set scripts.lint="ng lint" scripts.test="ng test --watch=false"
npm run api:generate                     # crea libs/api-client/src/generated y se COMMITEA
```

**4.5 Ajustes obligatorios:**
- `tsconfig.json`: `"strict": true`, `"noImplicitReturns": true`, `"noPropertyAccessFromIndexSignature": true`, `"strictTemplates": true` en `angularCompilerOptions`.
- Importar `libs/design-system/tokens.css` en el `styles.scss` de las tres apps; poner `data-app="tienda|panel|motorizado"` en el `<html>` de cada `index.html`.
- `angular.json`: presupuesto `initial` de la tienda: `maximumWarning: 350kb`, `maximumError: 500kb`.
- Proxy de desarrollo (`proxy.conf.json`) de las tres apps: `/api` → `http://localhost:8080`, `/app` (Reverb) → `ws://localhost:8085`.
- `libs/dominio`: crear `estado-pedido.ts` reflejando el enum del contrato y `dinero.ts` (formato `S/ 1 234,50`).

**4.6 Verificar:**
```bash
npx ng lint && npx ng test --watch=false && npx ng build tienda && npx ng build panel && npx ng build motorizado
```

**4.7 Primer PR del frontend:** `feat: workspace angular con tres apps, design system y cliente generado`. Debe mostrar en las tres apps una pantalla que consuma `GET /api/v1/salud` con el cliente generado y use los tokens.

## 5. Contrato: flujo de trabajo diario

1. Quien necesite un endpoint nuevo abre un PR **solo con el cambio en `contracts/openapi.yaml`** (y en `events/` si aplica). El otro lo revisa: nombres, formas, códigos de error.
2. Merge del contrato → ambos trabajan en paralelo: el backend lo implementa, el frontend regenera el cliente y construye la pantalla contra un mock (`npx --yes @stoplight/prism-cli mock ../contracts/openapi.yaml` sirve la API falsa en el puerto 4010).
3. El CI garantiza que el backend cumple el contrato y que el cliente generado está actualizado.

## 6. Flujo Git resumido (ADR-004)

```
git switch -c feat/reservas-inventario      # rama corta, ≤ 2 días
# commits pequeños: feat(inventario): reservar stock con bloqueo de fila
git push -u origin HEAD                      # abrir PR con la plantilla
# CI verde + 1 aprobación del otro desarrollador → Squash and merge
```
Prefijos: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`. Alcance entre paréntesis = módulo (`pedidos`, `tienda`, `motorizado`, `infra`).

## 7. Definición de "entorno listo" (cierra este documento)

- [ ] Repositorio en GitHub con `main` protegida y CODEOWNERS reales
- [ ] `make up` levanta todo en la máquina de ambos desarrolladores
- [ ] `art php artisan test` en verde con `EstadoPedidoTest`
- [ ] `deptrac`, `phpstan`, `pint` en verde
- [ ] Las tres apps Angular compilan y muestran `/salud`
- [ ] Primer PR de cada lado mergeado con CI verde
- [ ] Mailpit recibe un correo de prueba (`art php artisan tinker` → `Mail::raw('hola', fn($m) => $m->to('a@b.c'))`)

## 8. Siguiente paso (7): desarrollo módulo a módulo

Orden propuesto, cada uno con contrato → backend → frontend → E2E:
1. **Shared**: outbox, idempotencia, ProblemDetails, auditoría, `make:module`.
2. **Identidad** (login usuario/contraseña, roles, MFA admin) — necesario para todo el panel.
3. **Catálogo + Inventario** — tienda navegable con disponibilidad en tiempo real.
4. **Zonas + Clientes + Pedidos** — checkout completo.
5. **Riesgo** — confirmación automática y manual.
6. **Fulfillment** — preparación, rutas, app del motorizado con offline.
7. **Cobranza** — cobros y cierre de caja.
8. **Notificaciones** — correos, avisos, "avísame cuando haya stock".
