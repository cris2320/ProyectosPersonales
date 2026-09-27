# D'Too Limpieza

E-commerce de productos de limpieza con pago contraentrega. Angular + Laravel + MySQL.

**Estado al 26/09/2026:** documentación y código inicial; todavía falta integrar los esqueletos Laravel/Angular y ejecutar CI. Cristhian Rodriguez Ruiz desarrollará, probará y decidirá el proyecto, con **20 h semanales**. El plan de tres meses abarca **26/09–26/12/2026** y propone validar las fundaciones; el lanzamiento comercial no tiene fecha comprometida.

Consulta primero la [auditoría del repositorio](docs/10-auditoria-del-repositorio.md), las [decisiones pendientes](docs/11-decisiones-pendientes.md) y el [roadmap actualizado](docs/08-roadmap-y-plan-scrum.md). El alcance propuesto requiere la decisión final de Cristhian.

**Preparación del entorno autorizada:** empezar por [12 · Prerrequisitos e instalación en Windows](docs/12-prerrequisitos-windows.md). Los cierres técnicos siguen sujetos a sus pruebas; no se ha instalado software todavía.

## Documentación
| Doc | Contenido |
|---|---|
| [01 Visión](docs/01-vision-y-requisitos-de-calidad.md) | Qué construimos y con qué calidad |
| [02 Dominio](docs/02-modelo-de-dominio.md) | Módulos, vocabulario, estados del pedido |
| [ADRs](docs/adr/README.md) | Decisiones técnicas |
| [03 Arquitectura](docs/03-arquitectura.md) | Cómo está construido |
| [04 Datos](docs/04-modelo-de-datos.md) | Tablas MySQL |
| [05 UX](docs/05-diseno-ux.md) | Pantallas y flujos |
| [06 Repositorio](docs/06-repositorio-y-pipeline.md) | Cómo levantar y trabajar |
| [07 Bloque 1](docs/07-desarrollo-bloque-1-shared-identidad.md) | Shared e Identidad: integración y uso |
| [08 Roadmap](docs/08-roadmap-y-plan-scrum.md) | Tres meses, una persona, 20 h/semana; tareas con fechas y horas |
| [09 Cumplimiento](docs/09-cumplimiento-y-calidad.md) | Legal, privacidad, accesibilidad, terceros, pruebas |
| [10 Auditoría](docs/10-auditoria-del-repositorio.md) | Evidencia, brechas y correcciones priorizadas |
| [11 Decisiones](docs/11-decisiones-pendientes.md) | Documentos abiertos y decisiones finales |
| [12 Prerrequisitos](docs/12-prerrequisitos-windows.md) | Instalación de programas en Windows/WSL, Docker y comprobaciones |
| [Cronograma CSV](docs/cronograma-3-meses.csv) | 16 tareas propuestas, 180 h, dependencias y aceptación |
| [Backlog replanificado](docs/backlog-replanificado.csv) | 88 historias conservadas; responsable actual y fechas propuestas |

## Arranque rápido

**Prerrequisito:** completar la [guía 06](docs/06-repositorio-y-pipeline.md) y las correcciones de integración del [10](docs/10-auditoria-del-repositorio.md). Los comandos siguientes describen el entorno objetivo; no funcionan todavía con esta copia sin los esqueletos, dependencias y archivos de entorno de ejemplo que faltan.

```bash
cp infra/.env.example infra/.env
make up        # MySQL, Redis, API, Nginx, Reverb, workers, scheduler, frontends, Mailpit
make migrate   # migraciones + seeders
```
Tienda http://localhost:4200 · Panel http://localhost:4201 · Motorizado http://localhost:4202 · API http://localhost:8080/api/v1 · Correos de prueba http://localhost:8025

## Estructura
```
docs/       documentación (docs as code)
contracts/  openapi.yaml y esquemas de eventos — fuente de verdad de la API
backend/    Laravel (API), módulos en app/Modules
frontend/   workspace Angular: apps/tienda, apps/panel, apps/motorizado, libs/*
infra/      docker-compose, Dockerfiles
.github/    CI, plantilla de PR, CODEOWNERS, Dependabot
```

## Reglas del repositorio
- ADR-004 establece `main` protegida y PR con revisión cruzada. El equipo unipersonal confirmado requiere un nuevo ADR para adaptar esa aprobación; ver 08 §5. Las protecciones remotas no se han verificado ni cambiado.
- Conventional Commits. CI verde obligatorio.
- Cambios de API empiezan en `contracts/openapi.yaml`.
- Ningún módulo importa de otro fuera de `Contracts/` y `Events/` (deptrac lo verifica).
