# D'Too Limpieza

E-commerce de productos de limpieza con pago contraentrega. Angular + Laravel + MySQL.

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
| [08 Roadmap y Scrum](docs/08-roadmap-y-plan-scrum.md) | Plan de 12 semanas, backlog, Scrum |
| [09 Cumplimiento](docs/09-cumplimiento-y-calidad.md) | Legal, privacidad, accesibilidad, terceros, pruebas |

## Arranque rápido
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
- `main` protegida; todo entra por PR pequeño con revisión cruzada (ADR-004).
- Conventional Commits. CI verde obligatorio.
- Cambios de API empiezan en `contracts/openapi.yaml`.
- Ningún módulo importa de otro fuera de `Contracts/` y `Events/` (deptrac lo verifica).
