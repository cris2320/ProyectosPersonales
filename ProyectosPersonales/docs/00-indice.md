# D'Too Limpieza · Índice de documentación

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza — e-commerce de productos de limpieza con pago contraentrega |
| Dueño del producto | Cristhian Rodriguez Ruiz |
| Fecha del paquete | 2026-09-25 |
| Elaborado entre | 2026-09-11 y 2026-09-25 |

Este paquete reúne, sin omisiones, toda la documentación producida hasta la fecha. El estado de cada documento es el que figura en su propia cabecera.

## Documentos

| # | Archivo | Contenido | Estado |
|---|---|---|---|
| 01 | `01-vision-y-requisitos-de-calidad.md` | Problema, visión, segmentos, dimensionamiento operativo (3 motorizados, 2 almacén, 7:00–21:00 lun–dom), alcance de fase 1 y futuras, métricas de éxito, requisitos de calidad, restricciones, riesgos, glosario, parámetros confirmados, historial de versiones | v1.1 — Cerrado. Aprobado 2026-09-14 |
| 02 | `02-modelo-de-dominio.md` | Lenguaje ubicuo, mapa de 10 bounded contexts, agregados e invariantes por módulo, máquina de estados del pedido, recorridos, catálogo de eventos, preparación para fases futuras, parámetros confirmados, historial | v1.0 — Cerrado. Aprobado 2026-09-14 |
| — | `adr/README.md` | Índice del registro de decisiones y reglas para crear ADRs | Aceptado |
| — | `adr/plantilla.md` | Plantilla para nuevos ADRs | — |
| — | `adr/ADR-001-angular-frontend.md` | Angular para tienda, panel y app del motorizado (un workspace, tres apps) | Aceptado 2026-09-14 |
| — | `adr/ADR-002-laravel-backend.md` | Laravel como API pura; componentes estándar del proyecto | Aceptado 2026-09-14 |
| — | `adr/ADR-003-mysql.md` | MySQL 8 y convenciones de datos (dinero, ULID, fechas, espacial, concurrencia, migraciones) | Aceptado 2026-09-14 |
| — | `adr/ADR-004-github-monorepo-flujo.md` | GitHub, monorepo, trunk-based, PR con revisión cruzada, CI | Aceptado 2026-09-14 |
| — | `adr/ADR-005-monolito-modular.md` | Módulos bajo `app/Modules` con fronteras verificadas; MVC dentro de cada módulo | Aceptado 2026-09-14 |
| — | `adr/ADR-006-comunicacion-entre-modulos.md` | Contratos síncronos, eventos vía outbox, OpenAPI primero, Idempotency-Key | Aceptado 2026-09-14 |
| — | `adr/ADR-007-tiempo-real.md` | Laravel Reverb (WebSockets) y canales | Aceptado 2026-09-14 |
| — | `adr/ADR-008-pwa-offline-motorizado.md` | PWA con cola local de operaciones idempotentes | Aceptado 2026-09-14 |
| 03 | `03-arquitectura.md` | arc42 + C4: contexto, contenedores, componentes, escenarios de ejecución, despliegue fase 1 (CDN opcional, storage en disco), transversales, trazabilidad requisitos→mecanismos, riesgos | v1.0 — Cerrado. Aprobado 2026-09-14 |
| 04 | `04-modelo-de-datos.md` | 38 tablas MySQL por módulo + adenda `not_avisos_stock`, consultas críticas, retención, orden de migraciones, decisiones de negocio confirmadas (IGV incluido, método de pago obligatorio, "pago con", login por usuario) | v1.0 (+ adendas 1.0.1 y 1.0.2) — Cerrado. Aprobado 2026-09-14 |
| 05 | `05-diseno-ux.md` | Principios, taxonomía confirmada, tienda (inicio, categoría, producto, carrito, checkout 3 pasos, confirmación, Mi pedido, cuenta), panel de operación, app del motorizado, estados y microcopy, design system (paleta azul/amarillo), métricas UX, plan de validación, decisiones confirmadas | v1.0 — Cerrado. Aprobado 2026-09-14 |
| 06 | `06-repositorio-y-pipeline.md` | Contenido del paquete de repositorio, pasos del dueño en GitHub, guía backend (Laravel), guía frontend (Angular), flujo de contrato, flujo Git, checklist de "entorno listo", orden del paso 7 | Borrador v0.1 — se cierra al completar su §7 en el Sprint 0 |
| 07 | `07-desarrollo-bloque-1-shared-identidad.md` | Descripción del código base entregado (Shared e Identidad), integración en Laravel, patrón de uso, decisiones menores, trabajo paralelo del frontend, nota para el dueño | Entregado para integración — se cierra cuando las pruebas pasen en CI |
| 08 | `08-roadmap-y-plan-scrum.md` | Inventario de documentación, prerrequisitos, capacidad y opciones de equipo (A/B/C), roadmap de 12 semanas con hitos, marco Scrum con Linear (roles, eventos, DoR, DoD, métricas), backlog por sprint incl. E7, riesgos, fase 1.1 | Borrador v0.2 — pendiente: fecha de inicio y opción de equipo |
| 09 | `09-cumplimiento-y-calidad.md` | Cumplimiento legal (privacidad, términos, reembolsos, Libro de Reclamaciones, datos del negocio, afirmaciones, copyright, reseñas), privacidad práctica (datos necesarios, consentimientos, cookies, analítica), accesibilidad verificable, terceros y control de costos, rate limits, pruebas, Linear + IA, otros riesgos; anexos A–E | Borrador v0.1 — para aprobación del dueño; 4 pendientes del dueño en §9 |
| — | `backlog.csv` | Product Backlog: 88 historias (432 puntos en fase 1 + 8 en fase 1.1), con criterios de aceptación, responsable, sprint, MoSCoW y dependencias | Listo para importar a Linear |

## Paquete complementario

`dtoo-limpieza.zip` (entregado por separado) contiene el repositorio inicial: esta misma documentación en `docs/`, configuración de Docker y CI, contrato OpenAPI v0.2.0, esquema del evento `PedidoCreado.v1`, verificación de fronteras (deptrac) y el código del bloque 1 (Shared e Identidad, 48 archivos PHP con pruebas).

## Pendiente que no forma parte de este paquete (por diseño, se produce dentro del proyecto)

ADR-009 (despliegue y mapas), ADR-010 (observabilidad), ADR-011 (correo), contratos OpenAPI v0.3–v0.6, runbooks, guías de usuario, política de privacidad y términos, plan de pruebas de aceptación. Están planificados como historias del backlog (Sprint 0 y Sprint 5).
