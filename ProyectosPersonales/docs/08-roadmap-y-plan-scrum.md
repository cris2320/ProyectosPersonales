# 08 · Roadmap de tres meses: primera base de servicios independientes

| Campo | Valor |
|---|---|
| Actualización | 30/09/2026 · v0.5, ejecución iniciada |
| Inicio formal / horizonte | **26/09/2026–26/12/2026** |
| Responsable único | **Cristhian Rodriguez Ruiz: desarrollo, pruebas y decisiones** |
| Dedicación confirmada | **20 h/semana en total** |
| Dirección confirmada | Servicios con despliegue, escalado y trazabilidad independientes; [ADR-013](adr/ADR-013-servicios-independientes.md) |
| Alcance y estimaciones | Incremento autorizado para ejecución por Cristhian el 30/09; estimaciones revisables, cierres sujetos a evidencia |
| Tareas vigentes | [cronograma-3-meses.csv](cronograma-3-meses.csv) |
| Plan anterior | [Roadmap previo](historico/08-roadmap-y-plan-scrum-antes-servicios-2026-09-30.md) y [CSV previo](historico/cronograma-3-meses-antes-servicios-2026-09-30.csv), sustituidos |

## 1. Resultado del trimestre

Dos servicios ejecutables e independientes: **Identidad y Catálogo mínimo**, un panel Angular mínimo, contratos, entrada HTTP, bases con permisos separados, publicación de eventos a un consumidor técnico, trazas distribuidas y evidencia de carga/aislamiento/despliegue. El usuario autorizó continuar con este plan el 30/09/2026. Sus horas son estimaciones que se recalibran con ejecución real; esa autorización no acepta tareas todavía sin comprobar.

Catálogo solo incluye una operación protegida para crear producto sintético y su consulta pública. No incluye el catálogo comercial completo, precios históricos, gestión de imágenes ni disponibilidad real. El consumidor técnico prueba outbox/inbox y recuperación; no es Notificaciones terminado. El primer login debe incluir MFA del administrador y pruebas de revocación/permisos.

Este incremento **no habilita ventas** ni completa E0-01–E0-10. Se aplazan la construcción completa de tres apps, el design system amplio y los recorridos operativos del plan anterior. Inventario, Pedidos, Fulfillment, Cobranza y Notificaciones comercial permanecen en el backlog; no se promete siete servicios funcionales para diciembre.

## 2. Capacidad y fechas reales

Se conserva el inicio formal del 26/09. Hoy es 30/09: no existe evidencia de ejecución que permita marcar como terminadas las tareas previstas para 28–29/09. La nueva asignación comienza el **30/09/2026**, sin contabilizar retroactivamente esas horas como realizadas.

| Concepto | Horas |
|---|---:|
| 59 días disponibles del 30/09 al 24/12, a 4 h/día | 236 brutas |
| Trabajo enfocado, incluidas pruebas por tarea: 59 × 3 | 177 |
| Gestión, revisión, documentación transversal e imprevistos: 59 × 1 | 59 |
| Diez tareas propuestas S01–S10 | **168** |
| Reserva enfocada sin nuevas funciones | **9** |

Supuesto de lunes a viernes; se excluyen 08/10, 08/12 y 09/12. El 25/12 queda fuera del trabajo y el 26/12 es corte administrativo. Se mantiene el calendario de feriados de la línea base. Ninguna semana supera 20 h ni se suman las horas de gestión por fuera de esa capacidad.

Las 168 h son una estimación inicial por tareas, no una conversión de los 440 puntos históricos ni una garantía de entrega. Instrumentación, integración de librerías o instalación pueden requerir más; si ocurre, se reduce el incremento o se amplía fecha explícitamente. No se recortan las pruebas de seguridad para mantener una fecha.

## 3. Plan por partes

| Parte | Trabajo | Puerta de salida |
|---|---|---|
| 1 · Primera etapa, octubre | S01–S03 y comienzo de S04: límites, entorno y proyectos separados | Imágenes/CI independientes, bases separadas y contratos de identidad/eventos definidos |
| 2 · Noviembre | Finalizar S04; S05–S07: identidad, catálogo mínimo, panel y eventos | Recorrido mínimo y reentrega sin duplicación de efectos locales |
| 3 · Diciembre | S08–S10: trazas, métricas, carga/aislamiento, rollback y aceptación | Evidencias de independencia, recuperación y trazabilidad; siguiente incremento reestimado |

Las fechas exactas siguientes gobiernan la asignación; las partes mensuales no agregan trabajo paralelo. OpenTelemetry y contexto se diseñan desde S01 y se incorporan en cada servicio; S08 integra el visor, métricas y validación completa.

## 4. Tareas y tiempos

| ID | Fechas de 2026 | Horas | Trabajo |
|---|---|---:|---|
| S01 | 30/09–05/10 | 12 | Definir límites, contratos iniciales, identidad, broker y perfil de aceptación |
| S02 | 06/10–12/10 | 12 | Validar Windows/WSL/Docker y preparar infraestructura local por perfiles |
| S03 | 13/10–20/10 | 16 | Crear dos proyectos Laravel con imágenes y CI independientes |
| S04 | 20/10–02/11 | 28 | Integrar Identidad mínimo, MFA y autenticación entre servicios |
| S05 | 02/11–11/11 | 20 | Implementar catálogo mínimo con escritura protegida y lectura pública |
| S06 | 11/11–18/11 | 16 | Integrar entrada HTTP y panel Angular mínimo con clientes de contrato |
| S07 | 18/11–27/11 | 20 | Implementar outbox y consumidor técnico con inbox e idempotencia |
| S08 | 27/11–04/12 | 16 | Instrumentar trazas, logs, métricas y alertas mínimas por servicio |
| S09 | 04/12–15/12 | 16 | Probar carga, límites, aislamiento, dos réplicas y rollback independiente |
| S10 | 16/12–21/12 | 12 | Demostrar incremento, conciliar documentación y reestimar negocio |
| Reserva | 22/12–24/12 | 9 | Incidencias y ajustes; sin nuevas funciones |
| **Total tareas** | | **168** | Incluye pruebas por tarea y aceptación final |

Cada día dispone de 3 h enfocadas. Si dos tareas comparten fecha, se dividen ese bloque; no se ejecutan como si hubiera dos personas. El [CSV vigente](cronograma-3-meses.csv) registra dependencias secuenciales y criterios de salida. Las tareas T01–T16 del plan anterior quedan históricas, no completadas ni sumadas a S01–S10.

### Estado de ejecución al 30/09/2026

**S01 en curso**, inicio real 30/09; **0/10 tareas principales completadas**. Mapa de datos, borrador de contratos y perfil de aceptación elaborados; faltan compatibilidad conjunta y formalización/validación de contratos. Evidencia en [expediente S01](seguimiento/S01-fundaciones-servicios.md). S02–S10 permanecen pendientes, sin inicio/cierre real.

El [tablero del calendario](13-seguimiento-scrum.md) y el CSV registran estados, fechas reales, evidencia y pendientes. Las fechas de §4 son la línea base; nunca se usan como fecha real de cierre sin ejecución. Los avances documentales parciales se marcan dentro del expediente, sin cerrar la tarea principal.

## 5. Cadencia y reglas de trabajo

Una tarea de implementación en curso; registrar horas y evidencia al terminar cada bloque. Revisar alcance/capacidad semanalmente y demostrar avances quincenalmente, usando la bolsa de gestión. Al terminar S02 se recalibra el resto con tiempo real de instalación e integración. Si esa revisión ocurre después de la fecha prevista, se recalculan las tareas siguientes; no se mantienen fechas retrospectivas como promesas.

ADR-004 aún requiere adaptar la revisión al equipo individual mediante un nuevo ADR: propuesta de PR, auto-revisión explícita y checks obligatorios, con revisión externa cuando esté disponible. No se han modificado protecciones remotas ni se simula aprobación de una segunda persona. Las decisiones de proveedores y costos se documentan antes de conectar o contratar servicios.

## 6. Definition of Ready y Done

Cada tarea inicia con contrato/criterio de salida, dependencias y estimación revisados. Se acepta con pruebas relevantes y evidencia, no al llegar su fecha. Checks de contrato/build/calidad por servicio; MySQL real para persistencia, permisos y concurrencia; fallos/reintentos en broker real para eventos.

La aceptación final exige los criterios del ADR-013: despliegue independiente, dos réplicas, aislamiento SQL, traza HTTP/asíncrona, recuperación sin duplicar efectos, aislamiento medido bajo carga y CI. El perfil de carga define hardware, datos, duración, solicitudes por segundo, concurrencia y umbrales antes del ensayo. No se extrapola capacidad comercial de una prueba sintética mínima.

En S10 se actualizan las partes afectadas de 02/04/05/06/07 y se registran asuntos todavía abiertos. Ningún resultado de fundaciones constituye certificación completa de seguridad, accesibilidad, cumplimiento o alta disponibilidad productiva.

## 7. Trazabilidad del backlog

Se conservan **88 historias y 440 puntos históricos** (432 fase 1 + 8 fase 1.1) en [backlog.csv](backlog.csv) y [backlog-replanificado.csv](backlog-replanificado.csv). Sus criterios originales no se reescriben para fingir cumplimiento. La asignación anterior de E0 al trimestre se retira; las fechas por historia quedan vacías hasta dividir y reestimar los criterios para servicios.

S01–S10 son nuevas tareas de fundaciones distribuidas. Sus referencias a E0/E1 son parciales, no horas adicionales ni aceptación de esas historias completas. Ejemplo: S05 permite crear/consultar un producto para demostrar aislamiento, pero no entrega todo E1-02. Los hallazgos A03–A10 de la auditoría se comprueban al reutilizar cada pieza; los no cubiertos permanecen abiertos.

La lista completa de historias continúa siendo el alcance de referencia, sin fecha comercial. No se conserva el compromiso anterior de 35 puntos E0 en el trimestre porque su alcance y arquitectura han cambiado.

## 8. Incrementos posteriores y lanzamiento

Después de aceptar la base: ampliar Catálogo y construir Inventario; acordar estados/temporizadores y saga de Pedidos; después operación logística, cobranza, notificaciones y offline. Esa secuencia no fija meses: depende de nuevas estimaciones y de las decisiones de negocio del 11.

Antes de checkout deben reconciliarse 02/04/05, OpenAPI y eventos: intento pendiente, reserva/confirmación/vencimiento, compensación y recuperación manual. Antes de producción se prueban respaldo/restauración, despliegue, control de accesos y operación real; se cierran los requisitos aplicables del 09. No hay MVP de ventas aprobado en 168 h.

## 9. Decisiones vigentes

Confirmados: fecha inicial, responsable, 20 h/semana, servicios independientes y autorización para ejecutar el incremento de dos servicios del calendario. El mapa completo de siete servicios y las herramientas pendientes de validar siguen como diseño técnico propuesto. Pendientes: contrato final de identidad, broker/paquetes compatibles, proveedores/costos y validaciones de negocio. Ver [11](11-decisiones-pendientes.md). La instalación comienza con [12](12-prerrequisitos-windows.md); esta replanificación no instala programas ni crea servicios ejecutables.
