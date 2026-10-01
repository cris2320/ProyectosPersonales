# 13 · Seguimiento del calendario y evidencia de ejecución

Actualizado: **30/09/2026**, zona **America/Lima**. Responsable: **Cristhian Rodriguez Ruiz**.

## Plan autorizado y fuente de verdad

Cristhian indicó «continuemos entonces con lo planificado en el calendario» y pidió marcar las tareas realizadas. Se registra la autorización para ejecutar el incremento del [08](08-roadmap-y-plan-scrum.md). No constituye aceptación de código no probado ni aprobación automática de proveedores o gastos.

El [cronograma CSV](cronograma-3-meses.csv) es la fuente de verdad de las tareas S01–S10. `Inicio` y `Fin` conservan las fechas planificadas. `Inicio_real`, `Fin_real`, `Ultima_actualizacion`, `Evidencia` y `Pendiente` registran ejecución. `Horas_reales` queda vacío mientras no exista un registro fiable: las horas estimadas no se copian como horas consumidas ni el tiempo del asistente se atribuye a Cristhian.

## Tablero actual

| Tarea | Fechas planificadas | Estado | Inicio real | Fin real | Evidencia / pendiente |
|---|---|---|---|---|---|
| S01 · Límites, contratos, identidad, broker y aceptación | 30/09–05/10 | **En curso** | 30/09/2026 | — | [Expediente S01](seguimiento/S01-fundaciones-servicios.md). Diseño inicial elaborado; compatibilidad conjunta y revisión formal de contratos pendientes |
| S02 · Entorno Windows/WSL/Docker e infraestructura | 06/10–12/10 | Pendiente | — | — | Requiere cierre de S01 y checklist del 12; instalación no acreditada |
| S03 · Dos proyectos Laravel y CI | 13/10–20/10 | Pendiente | — | — | Requiere S02 |
| S04 · Identidad y MFA | 20/10–02/11 | Pendiente | — | — | Requiere S03 |
| S05 · Catálogo mínimo | 02/11–11/11 | Pendiente | — | — | Requiere S04 |
| S06 · Entrada HTTP y panel Angular | 11/11–18/11 | Pendiente | — | — | Requiere S05 |
| S07 · Outbox e inbox | 18/11–27/11 | Pendiente | — | — | Requiere S06 |
| S08 · Observabilidad | 27/11–04/12 | Pendiente | — | — | Requiere S07 |
| S09 · Carga, aislamiento y rollback | 04/12–15/12 | Pendiente | — | — | Requiere S08 |
| S10 · Aceptación del incremento | 16/12–21/12 | Pendiente | — | — | Requiere S09 |

**Tareas S completas: 0/10.** Los entregables documentales terminados dentro de S01 se identifican por separado en su expediente. No se convierten en historias E0/E1 aceptadas.

## Regla de actualización en cada sesión

1. Consultar la fecha real en America/Lima y elegir la primera tarea pendiente cuyas dependencias estén resueltas. Una fecha futura no prohíbe adelantar trabajo; registrar el adelanto real sin alterar retrospectivamente el plan.
2. Al comenzar, registrar `En curso` e `Inicio_real`. Mantener una tarea principal de implementación en curso.
3. Al entregar una parte, marcar solo esa parte como completa en el expediente, con fecha y enlace de evidencia.
4. Marcar la tarea principal `Completado` solo si todos sus criterios de salida se cumplen. Rellenar `Fin_real`, enlazar comprobaciones y vaciar `Pendiente`. No cerrar por alcanzar la fecha límite, crear una carpeta o aprobar un plan.
5. Si falta evidencia o algún criterio falla, mantener `En curso`; usar `Bloqueado` solo para un impedimento concreto, describiendo qué lo resuelve. Una tarea futura que espera su dependencia permanece `Pendiente`.
6. Actualizar juntos CSV, este tablero y la referencia de estado del 08. Registrar motivo y fecha de cualquier replanificación, conservando la línea base anterior.
7. Informar qué terminó, qué sigue abierto y el siguiente paso. No se ejecutan cambios de estado automáticamente al transcurrir los días ni se presupone vigilancia fuera de una sesión de trabajo.

`Completado` es cierre técnico con evidencia. La aceptación de un incremento por Cristhian se registra aparte, sin atribuirle una revisión que no haya realizado. Si se detecta una regresión, reabrir la tarea con motivo conservando su historial.

## Cadencia individual

Se usa una cadencia de trabajo individual inspirada en Scrum; no se simula un equipo de roles independientes. Planificar y revisar horas cada semana; demostrar avances quincenalmente. Revisiones previstas: **09/10, 23/10, 06/11, 20/11, 04/12 y 18/12**; aceptación técnica objetivo S10 **21/12**, reserva **22–24/12** y corte administrativo **26/12**. Todas las revisiones están pendientes hasta que exista acta/evidencia; su tiempo está dentro de la bolsa de gestión del 08.

## Bitácora

| Fecha real | Trabajo | Resultado |
|---|---|---|
| 30/09/2026 | Autorización de continuidad del usuario | Plan del 08 autorizado para ejecución; fechas y estimaciones conservadas |
| 30/09/2026 | Inicio de S01 | Mapa de datos, contrato inicial escrito, diseño de eventos y objetivos de aceptación elaborados; ver expediente |
| 30/09/2026 | Preparación del seguimiento | Estados normalizados y campos de ejecución añadidos; no se completan tareas sin evidencia |

## Próximo paso concreto

Terminar la revisión de contratos y la matriz de versiones/dependencias de S01. La documentación de proveedores permite comprobar requisitos declarados, pero no acredita que Composer resuelva todos los paquetes juntos. Para esa comprobación falta disponer de PHP/Composer o Docker: la preparación mínima de esas herramientas se puede adelantar desde S02 sin marcar toda S02 iniciada ni completada. La guía es [12 · Prerrequisitos](12-prerrequisitos-windows.md). Después del cierre de S01 se ejecuta S02 completa.
