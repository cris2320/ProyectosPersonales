> ARCHIVO HISTÓRICO: línea base anterior a la decisión del 30/09/2026. Sustituida por [ADR-013](../adr/ADR-013-servicios-independientes.md), [arquitectura vigente](../03-arquitectura.md) y [roadmap vigente](../08-roadmap-y-plan-scrum.md). Sus fechas y aprobaciones describen el plan anterior.

# 08 · Roadmap de tres meses y plan de trabajo individual

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Versión | Borrador v0.3 · actualizado el 26/09/2026 |
| Inicio confirmado | **26/09/2026** |
| Horizonte de tres meses | **26/09/2026–26/12/2026** |
| Desarrollo, pruebas y decisiones | **Cristhian Rodriguez Ruiz, una persona** |
| Dedicación confirmada | **20 horas semanales, incluyendo desarrollo, pruebas y documentación** |
| Estado del alcance | Propuesta de primer incremento; pendiente de decisión final de Cristhian |
| Evidencia de partida | [10 · Auditoría](../10-auditoria-del-repositorio.md) |
| Decisiones abiertas | [11 · Registro de decisiones](../11-decisiones-pendientes.md) |
| Tareas con fechas y horas | [cronograma-3-meses.csv](../cronograma-3-meses.csv) |
| Alcance completo replanificado | [backlog-replanificado.csv](../backlog-replanificado.csv) |
| Línea base anterior | [backlog.csv](../backlog.csv) y [roadmap v0.2 histórico](../historico/08-roadmap-v0.2.md) |

**Inicio autorizado:** Cristhian solicita comenzar por los prerrequisitos de instalación. Ver [12](../12-prerrequisitos-windows.md). Su aprobación global está condicionada a que no falte nada; como persisten decisiones y evidencias pendientes, el [11](../11-decisiones-pendientes.md) mantiene el alcance concreto de esa autorización y los cierres por verificar.

## 1. Resultado que se propone para el trimestre

**Entregar una base ejecutable, probada y preparada para desarrollar el negocio por módulos:** entorno local reproducible, CI, Laravel modular, workspace Angular de tres apps, contrato/cliente, Shared corregido, Identidad con MFA, componentes visuales base y login funcional del panel y motorizado.

El alcance corresponde a **E0-01–E0-10: 35 puntos históricos**, más trabajo de corrección detectado en la auditoría. No se consideran completados esos puntos por el código inicial existente: primero deben pasar las pruebas y los criterios de aceptación.

Este resultado **no habilita todavía ventas reales**. Catálogo, Inventario transaccional, checkout, rutas, entregas offline y caja siguen en el backlog. Si el resultado obligatorio de diciembre es vender, se debe aprobar y volver a estimar un MVP que complete un recorrido de negocio coherente. No basta trasladar etiquetas Must a otro sprint ni eliminar pruebas para forzar una fecha.

No se ha aprobado ningún recorte definitivo del producto. Se conserva todo el alcance original y se propone qué financiar con las horas disponibles de este trimestre.

## 2. Capacidad: cálculo y límites

El backlog contiene **88 historias y 440 puntos**: 87 historias/432 puntos en fase 1, de los cuales 415 Must y 17 Should; ocho puntos adicionales de reseñas en fase 1.1. La conversión anterior era `1 punto ≈ medio día enfocado`. Para poder contrastar números se interpreta ese medio día como cuatro horas; **es una hipótesis heredada que debe calibrarse**, no velocidad observada ni una equivalencia universal de Scrum.

| Concepto | Cálculo | Horas |
|---|---|---:|
| Referencia de 12 semanas | 12 × 20 h | 240 brutas |
| Calendario real propuesto del 28/09 al 24/12 | 61 días disponibles × 4 h | 244 brutas |
| Trabajo enfocado, incluidas pruebas de cada tarea | 61 × 3 h | 183 |
| Revisión de avances, documentación transversal, decisiones e imprevistos | 61 × 1 h | 61 |
| E0: diez historias originales | 35 puntos × 4 h | 140 |
| Revisión/correcciones de auditoría adicionales | T01 + T09 + T10 + T11 + T13 | 32 |
| Validación final y regresiones | T16 | 8 |
| **Total de tareas calendarizadas** | **140 + 32 + 8** | **180** |
| Reserva enfocada sin asignar | 183 − 180 | 3 |

Se supone distribución de **4 h de lunes a viernes**, sin fines de semana, y se excluyen 08/10, 08/12, 09/12 y 25/12. El 01/11 cae domingo y no reduce los días previstos. [Calendario oficial de feriados del Perú](https://www.gob.pe/feriados). Si prefieres trabajar otros días, se redistribuyen las mismas 20 h, sin sumarlas como capacidad adicional.

**Doce semanas no equivalen a tres meses calendario:** desde el 26/09, las primeras doce semanas terminan el 18/12 inclusive; el horizonte solicitado termina el 26/12. La última semana se usa para terminar login, aceptar el incremento y absorber ajustes. No es una semana de lanzamiento comercial. El sábado 26/09 queda como inicio y decisiones iniciales; el primer bloque de trabajo calendarizado comienza el lunes 28/09.

A la hipótesis de cuatro horas por punto, los **415 Must equivalen a 1 660 horas enfocadas**. A 15 h enfocadas por semana serían unas **111 semanas**, antes de feriados, aprendizaje y nuevo trabajo. Es una extrapolación de estimaciones sin calibrar, **no una fecha de entrega del producto**. Sí demuestra que el compromiso anterior de todo el producto en tres meses no tiene sustento para una persona a 20 h.

El margen es reducido. Si las correcciones exceden sus 32 h o la instalación requiere aprendizaje adicional, se reduce el alcance del trimestre o se cambia la fecha; no se consumen silenciosamente horas personales extra ni se rebajan garantías.

## 3. Plan por partes y meses

| Parte | Periodo | Trabajo | Entregable y puerta de salida |
|---|---|---|---|
| **1 · Decisiones y entorno** | 26/09–28/10 | T01–T06: alcance, flujo individual, ADRs de servicios, herramientas, Laravel, Angular, Docker y CI | Clon limpio reproducible; apps compilan; API de salud; primer PR con checks verdes. El 06 sigue abierto si falta alguna evidencia |
| **2 · Contrato y backend base** | 29/10–27/11 | T07–T13: contrato/cliente, Shared, concurrencia de idempotencia, outbox, auditoría, errores, Identidad/MFA | Pruebas de integración MySQL y regresiones de seguridad; backend base validado; no declarar el 07 cerrado con pruebas solo secuenciales |
| **3 · Interfaces de acceso y aceptación** | 30/11–26/12 | T14–T16: design system, login panel/motorizado, aceptación y siguiente etapa | Login real con MFA, componentes probados, clon limpio/CI y evidencias; decisión del alcance siguiente |

Las tres apps de T04 son esqueletos: SSR/PWA configurados no equivalen a tienda implementada ni sincronización offline operativa. Las pantallas comerciales se desarrollarán en incrementos posteriores.

## 4. Tareas y tiempos

Las fechas son una propuesta calculada secuencialmente a **máximo tres horas enfocadas por día disponible**. Dos tareas pueden compartir una fecha porque se divide el bloque de ese día; no se planifican dos desarrolladores ni ejecución simultánea. La suma diaria se mantiene dentro del límite.

| ID | Fechas de 2026 | Horas | Tarea / trazabilidad |
|---|---|---:|---|
| T01 | 28/09–29/09 | 6 | Alcance del trimestre, flujo individual y raíz Git; DEC-04/05/06 |
| T02 | 30/09–02/10 | 8 | ADR-009/010/011 y decisiones de infraestructura; E0-09 |
| T03 | 02/10–09/10 | 12 | Herramientas, Laravel modular y configuración de calidad; E0-03 |
| T04 | 09/10–20/10 | 20 | Workspace Angular: tres apps, cinco libs, SSR/PWA, strict y builds; E0-04 |
| T05 | 20/10–26/10 | 12 | Variables de ejemplo, Docker y arranque limpio integrado; E0-02 |
| T06 | 26/10–28/10 | 8 | Workflows, reglas GitHub y CI; E0-01 |
| T07 | 29/10–02/11 | 8 | Contrato, patrones MFA, cliente generado y verificación; E0-05/A09 |
| T08 | 02/11–11/11 | 20 | Integración Shared, migraciones y pruebas; E0-06 |
| T09 | 11/11–16/11 | 10 | Idempotencia concurrente, recuperación y replay; A03 |
| T10 | 16/11–18/11 | 6 | Outbox, fallos de consumidores y pruebas; A05/A06 |
| T11 | 18/11–19/11 | 4 | Errores, trace ID, configuración y auditoría; A08/A10 |
| T12 | 20/11–25/11 | 12 | Integración Identidad y MFA; E0-07 |
| T13 | 26/11–27/11 | 6 | Guards, autorización, revocación y cambio MFA; A04/A07 |
| T14 | 30/11–10/12 | 20 | Componentes base, contraste, teclado y accesibilidad; E0-10/A11 |
| T15 | 10/12–21/12 | 20 | Login panel/motorizado, QR, interceptores y pruebas; E0-08 |
| T16 | 21/12–23/12 | 8 | Aceptación, regresiones y planificación posterior |
| Reserva | 24/12 | 3 disponibles | No se asignan nuevas funciones; 25/12 sin trabajo; corte administrativo 26/12 |
| **Total tareas** | | **180** | **172 h de construcción/correcciones + 8 h de validación final** |

El [CSV de tareas](../cronograma-3-meses.csv) contiene dependencias, responsable, tipo de trabajo y criterio de salida de cada fila. Todas figuran como **Propuesto**, no como ejecutadas o aceptadas.

La planificación resuelve una dependencia práctica del backlog antiguo: el «entorno completo» necesita los esqueletos Laravel/Angular que allí se programaban después. Ahora primero se preparan las herramientas/esqueletos, luego se demuestra el arranque conjunto y se cierra CI. El repositorio local ya existe; no se repite su creación como si partiera de cero.

E0-06 solo puede aceptarse tras T11 y E0-07 tras T13. T07 verifica las operaciones ya implementadas e identifica contratos futuros; no debe declarar implementadas categorías/pedidos por figurar en OpenAPI. Los contratos futuros se añaden a la prueba de conformidad cuando se implementan.

## 5. Cadencia individual y revisiones

Se propone un flujo individual con entregas quincenales, límite de **una tarea de implementación en curso** y revisión semanal. No se simulan roles independientes ni revisión cruzada entre personas que no existen.

| Momento | Tiempo de gestión | Resultado |
|---|---:|---|
| Inicio de semana | 20 min | Elegir tareas según horas y dependencias reales |
| Cierre de cada bloque diario | 5 min | Horas, evidencia, impedimentos y siguiente paso |
| Revisión de cada viernes disponible | 30 min | Demo o evidencia, desviación de horas y ajuste |
| Revisión quincenal | 45 min dentro de la bolsa de gestión | Aceptar entregables y recalibrar estimaciones |
| Cierre del trimestre | Incluido en T16 | Informe de resultado real, pendientes y siguiente incremento |

Revisiones quincenales propuestas: **09/10, 23/10, 06/11, 20/11, 04/12 y 18/12**; aceptación final objetivo **23/12**. Se reserva el 24/12 para incidencias. Las ceremonias/documentación transversal usan la bolsa de 61 h; no se suman por encima de las 20 h semanales.

ADR-004 exige una segunda persona que apruebe PRs. Adaptarlo requiere un **nuevo ADR propuesto y decisión de Cristhian**, conservando el histórico. Propuesta: PR, auto-revisión identificada como tal, checks obligatorios y evidencias; revisión externa puntual cuando esté disponible. No se han cambiado protecciones remotas ni se ha dado por aprobado ese flujo.

## 6. Definition of Ready y Done

Una tarea entra en ejecución con criterio de salida, dependencias resueltas, decisión necesaria registrada y estimación revisada. Las compras/credenciales necesarias no se dan por existentes; si bloquean una integración, se adelanta otra tarea independiente y se registra el bloqueo.

Para aceptar una tarea:

- Código integrado mediante el flujo aprobado, con auto-revisión o revisión real documentada, sin describir una como la otra.
- Checks relevantes verdes: formato, tipos, fronteras, contrato y build; pruebas MySQL cuando afecta persistencia/concurrencia.
- Casos de error, reintento y autorización comprobados según el cambio. Un mock no sustituye la integración real.
- Cambios en docs y contratos trazables; horas reales y evidencia enlazadas al ID de la tarea.
- Secretos fuera del repositorio; datos sintéticos en pruebas y entornos de validación.
- Cristhian acepta el criterio de salida. Una tarea que falla queda pendiente; no se considera terminada al llegar su fecha.

Seguridad, accesibilidad y pruebas se aplican a cada incremento; no se reservan todas para el final. La aceptación de fundaciones tampoco constituye una certificación global ASVS/WCAG o legal del futuro producto.

## 7. Trazabilidad del alcance completo

El [backlog original](../backlog.csv) se conserva como línea base histórica: responsables BE/FE y sprints S0–S6 **no son asignaciones actuales**. [backlog-replanificado.csv](../backlog-replanificado.csv) conserva las 88 historias, prioridades, puntos, dependencias y criterios originales; añade el responsable actual, fechas propuestas solo para E0 y evidencia pendiente. Las referencias antiguas a «ambos», aprobación independiente y cuentas contratadas requieren adaptar sus criterios al tomar las decisiones del 11.

| Etapa propuesta | Historias/puntos de referencia | Programación actual |
|---|---|---|
| Fundaciones E0 | 10 / 35 | Primer trimestre; T01–T16 incluyen correcciones adicionales |
| Catálogo/Inventario E1 | 11 / 62 | Posterior, sin fecha comprometida |
| Pedidos/Zonas/Clientes E2 | 11 / 61 | Posterior, depende de Catálogo/Inventario |
| Riesgo/Fulfillment/Notificaciones E3 | 11 / 75 | Posterior, depende del flujo de pedidos |
| Motorizado/Cobranza E4 | 10 / 65 | Posterior, requiere idempotencia, rutas y caja |
| Tiempo real/calidad/operación E5 | 12 / 67 | Posterior; sus controles aplicables se incorporan antes a cada incremento |
| Lanzamiento E6 | 6 / 21 | Sin fecha; requiere flujo completo aceptado |
| Cumplimiento E7 fase 1 | 16 / 46 | Transversal; antes de cada funcionalidad afectada y de cualquier lanzamiento |
| Reseñas E7-07 fase 1.1 | 1 / 8 | Sin compromiso; «fase 1.1» no significa automáticamente mes 4 |
| **Total** | **88 / 440** | **432 de fase 1 + 8 de fase 1.1** |

Los puntos de E7 se contabilizan aparte de E0; redactar ADRs no equivale a implementar Sentry/Linear/alertas ni se marcan esas historias como completadas en el trimestre. E7-11/16 definen controles transversales: las pruebas del bloque inicial no cierran todos sus criterios de producto.

## 8. Condiciones para un MVP de ventas y para lanzamiento

Si Cristhian decide que diciembre debe incluir ventas, el próximo ejercicio debe separar historias y estimar un recorrido mínimo: catálogo → stock/cupo atómico → pedido → confirmación → preparación → entrega/cobro → conciliación → devoluciones/reclamaciones. Deben incluirse autenticación, auditoría, datos personales, respaldo y operación manual de contingencia. Cualquier simplificación de offline/tiempo real requiere revisar ADR-007/008 y aceptación de su impacto.

**No hay un MVP transaccional estimado y aprobado en 180 h.** Este documento no promete uno. Para conservar el alcance completo hay que ampliar plazo/capacidad; para conservar los tres meses hay que aceptar el incremento propuesto o aprobar otro alcance reestimado.

El lanzamiento comercial requiere evidencias de stock/cupo sin duplicados, cobros y caja íntegros, permisos por objeto, recuperación/rollback ensayados, funcionamiento de operación sin conexión según el alcance aprobado, datos/textos definitivos, atención de reclamos y aceptación de usuarios. Llegar al 26/12 no sustituye esos criterios.

## 9. Qué se decide primero

Ya están confirmados **fecha de inicio, responsable único y dedicación de 20 h/semana**. Queda decidir **si se acepta fundaciones como resultado del trimestre o si se necesita redefinir un MVP de ventas**, y después el flujo de revisión individual. El detalle y las demás decisiones están en el [11](../11-decisiones-pendientes.md).

El plan se recalcula después de las dos primeras semanas usando horas reales y al final de cada quincena. Los documentos 06/07 solo se cierran con evidencia; el 09 sigue pendiente de aprobación. No se da por aprobado un documento por actualizar sus fechas.
