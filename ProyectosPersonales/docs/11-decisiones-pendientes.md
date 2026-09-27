# 11 · Documentos abiertos y decisiones finales

Fecha de corte: **26/09/2026**. Decisor y responsable de desarrollo/pruebas: **Cristhian Rodriguez Ruiz**.

## Autorización para iniciar

Cristhian indicó: «dale todo aprobado a las documentaciones si ya no falta nada por agregar e iniciemos con el proyecto, pero primero dame todos los prerequisitos». Queda **autorizada la preparación del entorno y el inicio del trabajo previsto**. La condición de cierre integral aún no se cumple: faltan datos de negocio, proveedores, resolución de contradicciones y evidencias de pruebas descritas abajo. Por ello no se cambian todos los estados a «cerrado» ni se dan por aprobadas decisiones todavía sin contenido concreto. La instalación inicial está detallada en el [12](12-prerrequisitos-windows.md); no se ha instalado software en esta revisión.

## 1. Qué está aprobado y qué sigue en validación

«Aprobado» indica lo que declara la cabecera del documento; no prueba que esté implementado. No hay documentos formalmente etiquetados «en pruebas»: los estados reales son los siguientes.

| Documento | Estado declarado | Qué falta para cerrarlo |
|---|---|---|
| 01 · Visión | v1.1 cerrado | Incorporar equipo unipersonal y resolver precisión de disponibilidad/MFA; cambios de alcance requieren decisión |
| 02 · Dominio | v1.0 cerrado | Resolver temporizadores, consumo de stock, faltantes/devoluciones y privacidad del reconocimiento por teléfono |
| ADR-001 a ADR-008 | Aceptados | Aplicarlos y verificarlos; adaptar flujo de revisión de ADR-004 por nuevo ADR; reconciliar fuente OpenAPI 002/006 |
| 03 · Arquitectura | v1.0 cerrado | Formalizar proveedores/despliegue y probar calidad; coherencia de presupuesto JS y fronteras |
| 04 · Datos | v1.0 con adendas | La adenda 16c depende del 09 abierto: aprobar reclamaciones/reposiciones y asignar módulo; corregir inventario a 42 tablas propias |
| 05 · UX | v1.0 cerrado | Sin evidencia de pruebas de §9; validar prototipo con clientes, almacén y motorizados; corregir contraste y privacidad del autocompletado |
| **06 · Entorno y pipeline** | **Borrador v0.1** | Completar entorno, archivos faltantes, builds, herramientas de calidad y CI; aportar evidencias de §7 para una persona |
| **07 · Shared e Identidad** | **Entregado para integración** | Corregir A03–A10 del 10, ejecutar pruebas reales y CI; no está validado por haber sido entregado |
| **08 · Roadmap** | **Borrador actualizado el 26/09** | Inicio, responsable y 20 h/semana confirmados; decidir alcance del trimestre y recalibrar con velocidad real |
| **09 · Cumplimiento y calidad** | **Borrador v0.1** | Decisión del dueño, correcciones del 10 A11/A12, datos del negocio, política de devoluciones y presupuesto |
| 10 · Auditoría | Informe de evidencia al corte | Sus hallazgos se cierran con evidencia nueva; no declara código corregido |
| 11 · Este registro | Abierto | Registrar cada decisión de Cristhian con fecha e impacto |
| Backlog histórico | Inventario de alcance | No está validado como compromiso de tres meses; conservar criterios y trazabilidad al replanificar |

## 2. Documentos todavía inexistentes

| Entregable | Momento necesario | Dependencia / motivo |
|---|---|---|
| ADR-009 despliegue y mapas | Antes de comprar/configurar infraestructura y de geocodificación | Proveedor, presupuesto, datos enviados, respaldo y sustitución |
| ADR-010 observabilidad | Antes de integrar monitorización externa | Alertas, datos filtrados, costo y responsable |
| ADR-011 correo | Antes de correos a clientes | Proveedor, dominio/remitente y límites |
| **ADR-012 analítica** | Antes de instrumentar analítica | Aparece en 09/E7-10, pero faltaba en la lista de pendientes del 08 |
| ADR del flujo unipersonal | Antes de configurar reglas de PR/merge | ADR-004 exige revisión de otra persona inexistente en el equipo confirmado |
| OpenAPI posteriores a 0.2 y nuevos eventos | Antes de cada implementación | No son contratos ya entregados |
| Políticas finales: privacidad, términos, cookies, reembolsos | Antes de uso público/captura real | 09 contiene requisitos, no textos listos para publicar |
| Runbooks y ensayo de restauración/rollback | Antes de producción | No basta describir RPO/RTO |
| Plan/casos de aceptación e informes UX | Antes de aceptar flujos y antes del piloto | No hay pruebas con usuarios documentadas |
| Guías operativas y carga real de catálogo | Antes del piloto | Datos, formación y evidencia de recepción |

## 3. Orden de decisiones para trabajar por partes

| ID | Decisión que debe registrar Cristhian | Propuesta para revisar | Fecha objetivo | Estado |
|---|---|---|---|---|
| DEC-01 | Inicio del proyecto | 26/09/2026 | 26/09 | **Confirmado por el usuario** |
| DEC-02 | Equipo y decisión final | Cristhian desarrolla, prueba y decide | 26/09 | **Confirmado por el usuario** |
| DEC-03 | Horas semanales disponibles | 20 h/semana incluyendo desarrollo, pruebas y documentación | 26/09 | **Confirmado por el usuario** |
| DEC-04 | Resultado exigido el 26/12 | Fundaciones, Shared/Identidad, componentes y login; si se exige vender, redefinir y estimar un MVP transaccional antes de comprometerlo | 02/10 | Propuesto, no aprobado |
| DEC-05 | Regla de revisión unipersonal | PR + auto-revisión explícita + CI + evidencia; revisión externa opcional para cambios de mayor riesgo | 02/10 | Propuesto; nuevo ADR necesario |
| DEC-06 | Raíz del proyecto y CI | Mantener carpeta actual y workflows en raíz Git con rutas explícitas, salvo decisión de reorganizar | 02/10 | Propuesto |
| DEC-07 | Reconciliación técnica de docs | Contrato primero; alcance de idempotencia/auth; MFA/roles; presupuesto JS; correcciones del 10 | 09/10 | Pendiente |
| DEC-08 | Infraestructura y terceros | Presupuesto mensual total y por servicio; elegir VPS/mapas/correo/observabilidad antes de integrarlos | 16/10 | Pendiente |
| DEC-09 | Herramienta de seguimiento | Confirmar Linear y espacio de trabajo o continuar CSV; no es un bloqueo para programar | 16/10 | Pendiente; cuenta no verificada |
| DEC-10 | Datos de negocio | RUC, razón social, dirección, contacto; dominio, catálogo, fotos con permiso y polígono | Por entrega afectada | Pendiente; no publicarlos como datos de ejemplo reales |
| DEC-11 | Flujos de dominio | Resolver D-T03 a D-T06: reservas, preparación, faltantes, segundo fallo y recepción | Antes de Inventario transaccional/Pedidos | Pendiente |
| DEC-12 | Reconocimiento y derechos del cliente | Nunca exponer nombre/dirección por conocer un teléfono; acordar verificación y canal ARCO | Antes de Clientes/checkout | Pendiente |
| DEC-13 | Aprobación del 09 | Reembolsos/48 h, Libro, terceros, consentimientos, retención y requisitos aplicables; corregir plazos/contraste | Antes de flujos públicos | Pendiente |
| DEC-14 | Offline, expiración y recuperación | Preservar operaciones pendientes, definir reautenticación, conflictos y contingencia | Antes de la app operativa | Pendiente |
| DEC-15 | Lanzamiento | Solo con recorrido completo, pruebas, cumplimiento y operación aceptados | Tras cumplir puertas de salida | Sin fecha comprometida |

Las fechas son objetivos de decisión del plan propuesto, no aprobaciones automáticas. Las compras, conexiones de servicios y cambios remotos no se han realizado en esta revisión.

## 4. Secuencia de revisión contigo

1. **Parte 1 — diagnóstico:** revisar el dictamen del 10 y decidir el resultado del trimestre con las 20 h/semana confirmadas (DEC-04).
2. **Parte 2 — decisiones documentales:** resolver 06/07 y las contradicciones que afectan fundaciones; registrar el flujo individual.
3. **Parte 3 — implementación:** construir por tareas del cronograma, cerrando cada una con evidencia; revisar capacidad cada dos semanas.
4. **Parte 4 — validación y siguiente trimestre:** demostrar el incremento real, recoger pendientes y estimar la operación transaccional. Lanzar requiere su propio cierre de aceptación.

Formato para cada decisión: `ID · fecha · decisión de Cristhian · documentos/historias afectados · criterio de aceptación · nueva estimación si cambia el alcance`.
