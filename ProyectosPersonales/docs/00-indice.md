# D'Too Limpieza · Índice de documentación

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza — e-commerce de productos de limpieza con pago contraentrega |
| Dueño del producto | Cristhian Rodriguez Ruiz |
| Fecha de actualización | 2026-09-26 |
| Elaborado entre | 2026-09-11 y 2026-09-26 |
| Desarrollo y pruebas | Cristhian Rodriguez Ruiz; una persona, 20 h/semana |
| Inicio y horizonte | 26/09/2026–26/12/2026 |

Este paquete reúne, sin omisiones, toda la documentación producida hasta la fecha. El estado de cada documento es el que figura en su propia cabecera.

La aprobación documental no demuestra implementación. El [10](10-auditoria-del-repositorio.md) contrasta lo documentado con la copia local y el [11](11-decisiones-pendientes.md) identifica decisiones abiertas. El alcance/cronograma del trimestre es una propuesta pendiente de decisión final; fecha, responsable y dedicación sí están confirmados.

**Arranque autorizado por Cristhian:** comenzar por [12 · Prerrequisitos para Windows](12-prerrequisitos-windows.md). Quedan abiertas las decisiones y validaciones que impiden declarar toda la documentación cerrada.

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
| 04 | `04-modelo-de-datos.md` | 42 tablas propias enumeradas contando las adendas; consultas, retención y migraciones | v1.0 con adendas; base cerrada, adenda 16c vinculada al 09 pendiente de aprobación |
| 05 | `05-diseno-ux.md` | Principios, taxonomía confirmada, tienda (inicio, categoría, producto, carrito, checkout 3 pasos, confirmación, Mi pedido, cuenta), panel de operación, app del motorizado, estados y microcopy, design system (paleta azul/amarillo), métricas UX, plan de validación, decisiones confirmadas | v1.0 — Cerrado. Aprobado 2026-09-14 |
| 06 | `06-repositorio-y-pipeline.md` | Contenido del paquete de repositorio, pasos del dueño en GitHub, guía backend (Laravel), guía frontend (Angular), flujo de contrato, flujo Git, checklist de "entorno listo", orden del paso 7 | Borrador v0.1 — se cierra al completar su §7 en el Sprint 0 |
| 07 | `07-desarrollo-bloque-1-shared-identidad.md` | Descripción del código base entregado (Shared e Identidad), integración en Laravel, patrón de uso, decisiones menores, trabajo paralelo del frontend, nota para el dueño | Entregado para integración — se cierra cuando las pruebas pasen en CI |
| 08 | `08-roadmap-y-plan-scrum.md` | Capacidad individual, tres meses, 16 tareas con fechas/horas, aceptación y alcance posterior | Borrador v0.3; inicio, responsable y 20 h/semana confirmados; alcance propuesto |
| 09 | `09-cumplimiento-y-calidad.md` | Cumplimiento legal (privacidad, términos, reembolsos, Libro de Reclamaciones, datos del negocio, afirmaciones, copyright, reseñas), privacidad práctica (datos necesarios, consentimientos, cookies, analítica), accesibilidad verificable, terceros y control de costos, rate limits, pruebas, Linear + IA, otros riesgos; anexos A–E | Borrador v0.1 — para aprobación del dueño; 4 pendientes del dueño en §9 |
| 10 | `10-auditoria-del-repositorio.md` | Matriz documental/implementación, hallazgos y comprobaciones | Informe al 26/09; no certifica ejecución |
| 11 | `11-decisiones-pendientes.md` | Documentos abiertos, ausentes y decisiones priorizadas | Abierto; decisiones finales de Cristhian |
| 12 | `12-prerrequisitos-windows.md` | Programas, compatibilidad, instalación WSL/Docker, verificaciones y cuentas | Guía de preparación; instalación aún no ejecutada |
| — | `backlog.csv` | Línea base de 88 historias: 432 puntos fase 1 + 8 fase 1.1 | Histórico: sus responsables/sprints no son el plan actual |
| — | `backlog-replanificado.csv` | Las mismas 88 historias, responsable actual y fechas propuestas de E0 | Propuesta; ninguna historia marcada como aceptada |
| — | `cronograma-3-meses.csv` | 16 tareas, 180 h, fechas, dependencias y salida | Propuesta para 20 h semanales |
| — | `historico/08-roadmap-v0.2.md` | Roadmap previo para equipo de varias personas | Archivado; no vigente |

## Paquete complementario

El paquete anterior describía un `dtoo-limpieza.zip` entregado por separado. Ese ZIP no está en esta copia y no se ha verificado. La copia local tiene Docker, OpenAPI v0.2.0, `PedidoCreado.v1`, deptrac y 48 archivos PHP con pruebas; faltan los workflows de CI y otros archivos anunciados. Ver inventario del 10.

## Pendiente que no forma parte de este paquete (por diseño, se produce dentro del proyecto)

ADR-009 (despliegue y mapas), ADR-010 (observabilidad), ADR-011 (correo), ADR-012 (analítica), nuevo ADR de flujo unipersonal, contratos posteriores a v0.2, runbooks, guías, políticas finales e informes de aceptación. El 11 indica cuándo hacen falta; su programación anterior S0/S5 no equivale a fechas actuales.
