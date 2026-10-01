# 12 · Prerrequisitos e instalación del entorno Windows

Revisión inicial del equipo: 26/09/2026. Actualización de arquitectura: 30/09/2026. Responsable: Cristhian Rodriguez Ruiz. Esta guía prepara la máquina antes de integrar Laravel/Angular. Los comandos de instalación siguientes son instrucciones para ejecutar por pasos; no se han ejecutado durante esta revisión.

> Seguimiento al 30/09/2026: S01 está en curso; los prerrequisitos mínimos de esta guía pueden adelantarse para verificar dependencias. S02 completa sigue planificada para 06/10–12/10. Registrar únicamente comprobaciones ejecutadas en el [13](13-seguimiento-scrum.md).

## 0. Ajuste para servicios independientes

La dirección confirmada en [ADR-013](adr/ADR-013-servicios-independientes.md) cambia el despliegue objetivo, pero **los programas base para instalar siguen siendo Git, VS Code, WSL 2/Ubuntu y Docker Desktop**. Cada servicio tendrá su propio proyecto Laravel, dependencias, imagen y base/usuario; no es necesario instalar PHP/MySQL varias veces en Windows.

Infraestructura adicional propuesta, descargada como contenedores cuando se prepare el nuevo Compose:

| Componente | Uso | Estado |
|---|---|---|
| RabbitMQ | Broker candidato para eventos entre servicios | Validar cliente PHP, versión y consumo en S01/S02; no instalado |
| OpenTelemetry Collector | Recibir/exportar telemetría de los servicios | Propuesto; no instalado |
| Jaeger o Tempo | Visualizar trazas distribuidas | Elegir uno en ADR-010; no instalar ambos por defecto |
| Herramienta de carga, por ejemplo k6 | Medir tráfico, latencia y aislamiento | Seleccionar/fijar imagen antes de S09 |

Redis sigue siendo útil para caché y colas internas; Horizon no se da por compatible con el broker de eventos. Las dependencias OpenTelemetry de PHP se resolverán en las imágenes de los servicios, con versiones verificadas. Kubernetes, Kafka y herramientas nativas adicionales no son prerrequisitos del primer incremento.

Usar perfiles Compose para levantar solo los servicios del incremento. La recomendación de 16 GB del equipo es orientativa y debe medirse con broker/telemetría activos; no hay RAM verificada ni consumo real observado. Bases, colas, visor y collector se mantendrán en red interna; interfaces locales de diagnóstico se vincularán a loopback. Los puertos listados más abajo describen el Compose anterior, no el destino aprobado.

El Compose y Dockerfiles actuales aún corresponden al backend único. No se han modificado, instalado programas ni generado servicios durante esta revisión. El [08](08-roadmap-y-plan-scrum.md) programa su sustitución verificable; `make up` todavía no acredita el nuevo diseño.

## 1. Qué está comprobado en tu equipo

| Elemento | Resultado observado |
|---|---|
| Sistema | Windows, build 26200, arquitectura AMD64/x64; edición no verificada |
| Git | Instalado: `2.55.0.windows.5` |
| Visual Studio Code | Ejecutable disponible; ya estás usando el editor |
| WSL | `wsl.exe` existe, pero informa que el subsistema no está instalado |
| Docker, Node/npm, PHP, Composer, make | No encontrados en el PATH de esta sesión; no demuestra ausencia en toda la máquina |
| RAM, espacio libre y virtualización | Sin verificación concluyente: las consultas de hardware no devolvieron datos utilizables en esta sesión |

## 2. Requisitos de la computadora

- Windows de 64 bits con soporte vigente. Para este equipo, comprobar edición y versión con `winver`.
- Virtualización de hardware activada en BIOS/UEFI. Comprobar en Administrador de tareas → Rendimiento → CPU → Virtualización.
- Docker exige al menos **8 GB de RAM** y WSL **2.1.5 o posterior**. [Requisitos oficiales de Docker Desktop](https://docs.docker.com/desktop/setup/install/windows-install/).
- Para este proyecto recomiendo **16 GB de RAM o más** y **40–60 GB libres en SSD**. Son márgenes de trabajo propuestos para editor, imágenes, MySQL y tres aplicaciones, no mínimos oficiales del fabricante.
- Conexión a Internet para descargar paquetes e imágenes; acceso de administrador para habilitar WSL inicialmente; posibilidad de reiniciar Windows.

## 3. Programas que necesitas

| Programa | Dónde | Para qué | Acción |
|---|---|---|---|
| Git | Windows; también Ubuntu para la terminal Linux | Control de versiones | Conservar el de Windows; instalar paquete en Ubuntu |
| Visual Studio Code | Windows | Editor | Ya disponible; añadir extensión WSL |
| WSL 2 + Ubuntu 24.04 LTS | Windows | Terminal Linux para los comandos Bash y Makefile del proyecto | Instalar primero |
| Docker Desktop, edición para x86_64 | Windows | Ejecutar los servicios Linux del proyecto | Instalar y activar integración con Ubuntu |
| `git`, `make`, `rsync`, `curl`, certificados y `unzip` | Ubuntu | Utilidades del repositorio y preparación | Instalar con apt |
| Edge o Chrome actualizado | Windows | Navegación y pruebas con DevTools | Usar el disponible; otro navegador es opcional |
| Aplicación de códigos TOTP | Teléfono | MFA de GitHub y del administrador del proyecto | Usar la que ya tengas compatible con TOTP |

VS Code funciona en Windows conectado a Ubuntu mediante su [extensión WSL](https://code.visualstudio.com/docs/remote/wsl). Docker debe usar el motor WSL 2 y contenedores Linux, con la [integración de la distribución habilitada](https://docs.docker.com/desktop/features/wsl/).

## 4. Orden de instalación

### Paso 1 · Instalar WSL y Ubuntu

Abrir **PowerShell como administrador** y ejecutar:

```powershell
wsl --install -d Ubuntu-24.04
```

Reiniciar Windows si se solicita. Abrir Ubuntu desde Inicio y crear su usuario y contraseña Linux. Esta contraseña es local; no se comparte ni se escribe en el repositorio.

Después, en PowerShell:

```powershell
wsl --update
wsl --set-default-version 2
wsl --version
wsl --list --verbose
```

Esperado: WSL instalado/actualizado y Ubuntu con `VERSION 2`. Si la distribución aparece como versión 1, convertirla con `wsl --set-version Ubuntu-24.04 2`. El número de versión del programa WSL y la columna `VERSION` de la distribución son comprobaciones distintas. [Instalación oficial de WSL](https://learn.microsoft.com/en-us/windows/wsl/install).

### Paso 2 · Instalar Docker Desktop

Descargar el instalador **Windows x86_64** desde la [página oficial](https://docs.docker.com/desktop/setup/install/windows-install/). Seleccionar el backend WSL 2 y abrir Docker Desktop al terminar.

En Settings:

- General → `Use the WSL 2 based engine`.
- Resources → WSL Integration → habilitar `Ubuntu-24.04`.
- Aplicar cambios y esperar a que el motor esté listo.

Docker Desktop aporta Docker Engine, CLI y Compose. Para esta ruta de instalación se usa su integración; no hace falta instalar otro Docker Engine mediante apt en Ubuntu.

### Paso 3 · Instalar las utilidades Linux

Abrir la terminal **Ubuntu**, no PowerShell, y ejecutar:

```bash
sudo apt update
sudo apt install -y git make rsync curl ca-certificates unzip
```

Git dentro de Ubuntu tiene su propia configuración. Antes del primer commit, configurar tu nombre y tu correo de GitHub (o el correo privado noreply de GitHub):

```bash
git config --global user.name "Cristhian Rodriguez Ruiz"
git config --global user.email "TU_CORREO_DE_GITHUB"
```

Reemplazar el correo de ejemplo antes de ejecutar. El proyecto utilizará Git desde Ubuntu cuando se trabaje en esa terminal.

### Paso 4 · Preparar VS Code

Instalar **WSL, de Microsoft**, en VS Code para Windows. Conectar a Ubuntu mediante `WSL: Connect to WSL using Distro` y elegir Ubuntu-24.04.

Extensiones útiles para este proyecto, instaladas en el entorno remoto cuando corresponda:

| Extensión / editor | Utilidad |
|---|---|
| Angular Language Service / Angular | Plantillas Angular |
| ESLint / Microsoft | Diagnóstico de JavaScript/TypeScript |
| Prettier / Prettier | Formato |
| PHP Intelephense / Ben Mewburn | Análisis y autocompletado PHP |
| EditorConfig for VS Code / EditorConfig | Convenciones de edición |
| YAML / Red Hat | Compose, CI y contratos YAML |

Las extensiones no sustituyen los comandos de calidad del proyecto. Node y PHP locales solo se añadirán si una herramienta del editor los necesita; las compilaciones/pruebas del proyecto correrán inicialmente en contenedores.

La carpeta actual se ve desde Ubuntu como:

```text
/mnt/c/Users/repre/ProyectosPersonales/ProyectosPersonales
```

La raíz Git está en la carpeta superior. Antes de mover o crear otra copia, comprobar `git status` y preservar los cambios locales existentes. Docker recomienda almacenar el código usado por contenedores Linux en el sistema de archivos Linux para mejorar el rendimiento; se puede preparar esa copia después de preservar los cambios. [Guía de Docker con WSL](https://docs.docker.com/desktop/features/wsl/).

### Paso 5 · Validar el entorno

En **Ubuntu**, con Docker Desktop abierto:

```bash
git --version
make --version
docker version
docker compose version
docker info --format '{{.OSType}}'
docker run --rm hello-world
```

Esperado: `docker version` informa Client y Server; Compose funciona; el tipo es `linux`; `hello-world` termina correctamente. El último comando descarga una imagen de prueba. Si Docker no aparece en Ubuntu, revisar WSL Integration y volver a abrir la terminal.

Todavía no ejecutar `make up` como prueba del proyecto: faltan los archivos de entorno y esqueletos identificados en la auditoría. La validación anterior comprueba la máquina, no la aplicación.

## 5. Tecnologías que instalará el proyecto dentro de Docker

| Tecnología | Base prevista | Situación |
|---|---|---|
| PHP | 8.4 | Dockerfile existente; no requiere instalar PHP en Windows |
| Composer | 2.x | Incluido en la imagen PHP del proyecto |
| Laravel | 13.x como base candidata al generar el proyecto | Requiere PHP ≥ 8.3; PHP 8.4 satisface ese requisito. Paquetes y código inicial todavía deben validarse |
| Node.js + npm | Último parche compatible de **22.x**, mínimo **22.22.3** si se usa Angular 22 | Mantiene la familia `node:22-alpine` del repositorio; fijar parche y lockfile al integrar |
| Angular + CLI | 22.x como base candidata al generar el workspace | Dependencias del proyecto; evitar un CLI global sin versión fijada |
| MySQL | 8.4 | Servicio de Compose |
| Redis | 7 | Servicio de Compose |
| Nginx, Mailpit | Servicios de Compose | Revisar/fijar etiquetas al integrar |
| Sanctum, Horizon, Reverb, Pest/PHPUnit, PHPStan, Pint, deptrac | Paquetes Composer | Se resuelven y fijan al generar `composer.lock` |
| Cliente OpenAPI, lint, pruebas frontend y E2E | Paquetes npm | Se resuelven y fijan al generar `package-lock.json` |

La [matriz de Angular](https://angular.dev/reference/versions) exige para Angular 22 Node `^22.22.3`, `^24.15.0` o `^26.0.0`; no sirve cualquier parche antiguo de Node 22. Node 22 y 24 figuran como LTS en el [calendario oficial de Node](https://nodejs.org/en/about/previous-releases). Se conserva 22 para alinearse con el Dockerfile actual. [Laravel 13 requiere PHP ≥ 8.3](https://laravel.com/framework/docs/13.x/releases).

Estas versiones son una base de compatibilidad para preparar el entorno, no una certificación de todos los paquetes existentes. Se fijarán versiones exactas al resolver dependencias y pasar pruebas.

**Instalaciones separadas que no hacen falta para esta ruta:** XAMPP, WAMP, Laragon, MySQL Server, Redis Server, PHP o Composer en Windows; npm por separado; Angular CLI global. Si se decide ejecutar Angular fuera del contenedor, se instalará el mismo Node compatible dentro de Ubuntu y se ajustará el comando del frontend para evitar dos servidores sobre los mismos puertos.

## 6. Cuentas y herramientas opcionales

- **GitHub con 2FA:** necesario para alojar/verificar CI remoto; tener acceso al repositorio y un método de autenticación configurado.
- **Docker Hub:** cuenta opcional para iniciar; puede ayudar con los límites de descarga.
- **Linear:** no bloquea el arranque; el seguimiento puede continuar en los CSV existentes mientras se configura.
- **DBeaver Community** para explorar MySQL y **Bruno/Postman** para peticiones HTTP son opcionales. También se puede usar la terminal.
- VPS, dominio, correo transaccional, mapas, Sentry y analítica se decidirán en sus ADRs; no necesitas contratarlos para validar Docker localmente.
- El teléfono Android para pruebas offline se necesita en el incremento de motorizado; no bloquea la instalación inicial.

Puertos locales previstos en Compose: `3306`, `6379`, `8080`, `8085`, `4200–4202`, `8025` y `1025`. Comprobar conflictos antes de levantar la aplicación. Los puertos 4200/4201/4202 corresponden a tienda/panel/motorizado.

## 7. Checklist de salida de esta etapa

- [ ] RAM, espacio y virtualización comprobados.
- [ ] Ubuntu instalada en WSL 2 y actualizada.
- [ ] Docker Desktop arrancado e integrado con Ubuntu.
- [ ] `docker version`, `docker compose version` y `hello-world` correctos.
- [ ] VS Code conecta a Ubuntu; Git y make funcionan allí.
- [ ] Cambios del repositorio preservados; identidad Git configurada.
- [ ] Acceso GitHub preparado para la posterior configuración de CI.

Después de este checklist se preparan los esqueletos independientes del ADR-013 y se adapta la guía 06; el orden vigente es S01–S10 del 08. La aprobación para comenzar no equivale a cerrar las pruebas del 07 ni los requisitos de lanzamiento del 09.
