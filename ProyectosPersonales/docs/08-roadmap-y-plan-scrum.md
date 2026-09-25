# 08 · Roadmap, prerrequisitos y plan Scrum

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Estado | Borrador v0.2 — pendiente: fecha de inicio y decisión de equipo (§3). Actualizado el 2026-09-25 con la épica E7 del documento 09 |
| Fecha | 2026-09-14 |
| Duración objetivo | 12 semanas (3 meses) desde la fecha de inicio |
| Depende de | Documentos 01–07 y ADRs (todos aprobados) |
| Anexo | `backlog.csv` — 71 historias con criterios de aceptación, puntos, sprint, prioridad y dependencias (importable a GitHub Projects, Jira o Trello) |

---

## 1. Inventario de documentación

### 1.1 Completa y aprobada

| Doc | Contenido | Estado |
|---|---|---|
| 01 Visión y requisitos de calidad | Qué, para quién, con qué calidad; parámetros operativos | v1.1 cerrado |
| 02 Modelo de dominio | 10 módulos, vocabulario, máquina de estados, eventos | v1.0 cerrado |
| ADR-001 a 008 | Angular, Laravel, MySQL, GitHub, monolito modular, eventos/outbox, Reverb, PWA offline | Aceptados |
| 03 Arquitectura (arc42 + C4) | Contexto, contenedores, componentes, escenarios, despliegue, transversales | v1.0 cerrado |
| 04 Modelo de datos | 39 tablas MySQL, consultas críticas, retención, orden de migraciones | v1.0 cerrado (+ adenda) |
| 05 Diseño UX | Taxonomía, tienda, checkout, panel, app motorizado, design system, métricas | v1.0 cerrado |
| 06 Repositorio y pipeline | Estructura del monorepo, Docker, CI, guía de arranque | Listo; se cierra al completar §7 del propio documento |
| 07 Bloque 1 (Shared + Identidad) | Código base e instrucciones de integración | Entregado; se usa en Sprint 0 |

### 1.2 Pendiente (se produce dentro del proyecto, en Sprint 0 y 5)

| Doc | Contenido | Cuándo | Quién |
|---|---|---|---|
| ADR-009 | Despliegue (VPS, dominio, MySQL gestionado o no, CDN opcional) y proveedor de mapas | Sprint 0 | Backend + dueño |
| ADR-010 | Observabilidad (logs, métricas, alertas, uptime) | Sprint 0 | Backend |
| ADR-011 | Proveedor de correo saliente | Sprint 0 | Backend + dueño |
| Contratos OpenAPI v0.3–v0.6 | Un incremento por sprint, antes de codificar | Cada sprint | Ambos devs |
| Runbooks | Despliegue, rollback, restauración, incidente de datos, caída de Reverb, caja con diferencia | Sprint 5 | Backend |
| Guías de usuario (1 página c/u) | Administrador, almacén, motorizado | Semana 12 | Frontend + dueño |
| Política de privacidad y términos | Textos legales para la tienda | Sprint 5 | Dueño (revisión legal recomendada) |
| Plan de pruebas de aceptación | Casos por recorrido crítico para el piloto | Sprint 5 | Dueño + frontend |

No hay documentación técnica de diseño pendiente: todo lo que el equipo necesita para empezar está aprobado.

## 2. Prerrequisitos para iniciar el desarrollo

### 2.1 Del dueño (antes del día 1)

| # | Prerrequisito | Por qué bloquea |
|---|---|---|
| 1 | **Fecha de inicio** confirmada | Convierte las semanas del roadmap en fechas |
| 2 | **Decisión de equipo** (§3): 2 devs en 5 meses o 3 devs en 3 meses | Define qué backlog se compromete |
| 3 | Cuenta de GitHub (organización o personal) y usuarios de los desarrolladores | Sprint 0 arranca creando el repositorio |
| 4 | Dominio registrado (p. ej. `dtoolimpieza.pe` o `.com`) | Necesario para TLS, correo y SEO desde staging |
| 5 | Proveedor de VPS elegido y cuenta creada (presupuesto orientativo: US$ 20–40/mes) | ADR-009; staging debe existir desde Sprint 1 |
| 6 | Correo corporativo para envíos transaccionales (p. ej. `pedidos@dominio`) | ADR-011 |
| 7 | Polígono de la zona de cobertura (límites reales del distrito que atenderás) | Seeder de Zonas en Sprint 2 |
| 8 | Catálogo inicial en hoja de cálculo: producto, marca, categoría, presentación, SKU, precio hogar, precio negocio, existencia | Carga en Sprint 1 (ejemplo) y Semana 12 (real) |
| 9 | Fotos de productos (una por producto como mínimo) | Tienda navegable |
| 10 | Dos horas semanales reservadas para review y decisiones | Scrum no funciona sin el Product Owner presente |

### 2.2 Técnicos (cada desarrollador, día 1)

- Docker Desktop, Git, Node 22 LTS, editor con EditorConfig y ESLint/PHP Intelephense.
- Cuenta de GitHub con 2FA activado (requisito de seguridad del propio proyecto).
- Lectura obligatoria antes del Sprint 0: documentos 01, 02, 03 (§5, §6, §8), 05 y los 8 ADRs. Tiempo estimado: medio día. Se verifica con una sesión de preguntas el día 1.

### 2.3 Legales y operativos (no bloquean el código, sí el lanzamiento)

- Registro del banco de datos personales ante la ANPD (trámite gratuito en línea; iniciarlo en Sprint 3 para tener la constancia en la Semana 12).
- Política de privacidad y términos redactados (idealmente revisados por un abogado).
- Consulta con contador sobre emisión de comprobantes: fuera del sistema, pero la venta sí los requiere.
- Celulares Android de los 3 motorizados con Chrome actualizado y datos móviles.
- Impresora en almacén para la nota de pedido (térmica de 80 mm o cualquier impresora A5).
- Definir el esquema de turnos de los motorizados (para configurar cupos por franja).

## 3. Capacidad y decisión de equipo

Escala: **1 punto ≈ medio día de trabajo enfocado.** Backlog completo tras el 09: **88 historias, 432 puntos en fase 1** (≈ 215 días-persona) más 8 en fase 1.1. Should prescindibles en el lanzamiento: 17 puntos (E2-11, E5-04, E5-09, E6-06). **Must = 415 puntos.** Ver 09 §8: incluso en la Opción A hay que mover historias no legales a la fase 1.1 en la planning del S0.

Capacidad realista por desarrollador y sprint de 2 semanas: 10 días laborables − ceremonias, revisión de código e imprevistos ≈ **8 días efectivos ≈ 16 puntos**. En 12 semanas (Sprint 0 de 1 semana + 5 sprints + Semana 12 de lanzamiento) ≈ **~90 puntos por desarrollador**.

| Opción | Equipo | Capacidad | Resultado |
|---|---|---|---|
| **A · Recomendada** | Backend + Frontend + **Dev 3 full-stack** (Sprints 1–5, enfocado en el panel) | ~270 puntos + reparto del panel | Alcance Must completo en 12 semanas, **ajustado**: los Should solo si sobra tiempo. Requiere disciplina en el contrato-primero y cero cambios de alcance. |
| **B** | Backend + Frontend | ~180 puntos en 12 semanas | Alcance Must completo en **20 semanas** (Sprints 0–8). Mismo backlog, sprints reasignados. |
| **C** | Backend + Frontend, 12 semanas | ~180 puntos | Requiere recortar ~50 % del Must: sin app offline completa, panel de rutas sin mapa, sin tiempo real, sin cuenta ni ARCO automatizados. **No recomendada**: quita las piezas que evitan pérdidas en contraentrega. |

> Nota: incluso en la Opción A el plan no tiene holgura propia; la holgura es la lista Should (17 pts) y la Semana 12, que está dedicada a estabilizar y lanzar, no a construir. Si en el Sprint 2 la velocidad real está por debajo de lo previsto, se decide en la review de ese sprint mover historias a la fase 1.1 (mes 4), no acelerar.

## 4. Roadmap (12 semanas, Opción A)

Semana 1 = fecha de inicio. Las fechas exactas se rellenan al confirmarla.

```
Sem  1        2   3        4   5        6   7        8   9        10  11       12
     ┌──S0──┐ ┌────S1────┐ ┌────S2────┐ ┌────S3────┐ ┌────S4────┐ ┌────S5────┐ ┌─Lanz─┐
     Fundac.  Catálogo +   Checkout     Riesgo +     Motorizado   Tiempo real  Piloto
     y login  Inventario   completo     Preparación  offline +    Calidad      Go-live
              Tienda       Panel        Rutas        Caja         Seguridad
              navegable    pedidos      Correos                   Usuarios
Hito:  H0       H1           H2           H3           H4           H5           H6
```

| Sprint | Semanas | Objetivo del sprint (Sprint Goal) | Hito demostrable en la review |
|---|---|---|---|
| **S0** | 1 | Entorno, CI, Shared e Identidad integrados, design system base, ADRs 009–011 | **H0:** los tres devs hacen `make up`, CI verde, login en panel y app con MFA |
| **S1** | 2–3 | Catálogo e Inventario con reservas concurrentes; tienda navegable con SSR; panel de catálogo e inventario | **H1:** el dueño navega la tienda con 20 SKU reales, ve "agotado" al reservar la última unidad, edita un precio desde el panel |
| **S2** | 4–5 | Zonas, clientes, pedidos: checkout completo con mapa, franjas con cupo, método de pago y vuelto; consulta "Mi pedido"; panel de pedidos | **H2:** un pedido real de punta a punta desde el celular hasta la lista del panel; reintento no duplica |
| **S3** | 6–7 | Riesgo (auto/manual), preparación con nota impresa, rutas con mapa y vuelto por ruta, correos, pantalla Hoy, configuración | **H3:** un pedido nuevo cae en revisión, el dueño lo confirma con el botón, almacén lo prepara e imprime, el dueño lo pone en una ruta |
| **S4** | 8–9 | App del motorizado con offline, entregas y fallos, cobros inmutables, cierre de caja con diferencias, devoluciones | **H4:** un motorizado entrega 5 pedidos con datos apagados, sincroniza, declara caja; una diferencia exige justificación |
| **S5** | 10–11 | Tiempo real, avisos, "avísame", SEO, rendimiento, seguridad ASVS L2, ARCO, cuenta opcional, pruebas con usuarios, runbooks, observabilidad | **H5:** badge de stock cambia en vivo; Lighthouse verde; checklist ASVS firmado; informe de pruebas con usuarios |
| **Lanz.** | 12 | Producción, carga real del catálogo, ANPD, capacitación, piloto de 3 días, go-live | **H6:** primera semana operando con pedidos reales y métricas del 01 §5 midiéndose |

**Opción B (2 devs, 20 semanas):** mismos objetivos; S1–S5 pasan a durar 3 semanas cada uno y se añade un S6 de 2 semanas para lo que quede; lanzamiento en la semana 20.

### Dependencias críticas (ruta crítica)
Identidad → Catálogo/Inventario → Pedidos → Riesgo → Fulfillment → Cobranza → Tiempo real → Lanzamiento. El frontend nunca espera al backend gracias al contrato-primero y al mock (`prism`), pero **la integración real de cada módulo ocurre en el mismo sprint**: si el backend de Pedidos se atrasa en S2, la demo H2 se hace con mock y se marca deuda.

## 5. Marco Scrum

### 5.1 Roles

| Rol | Quién | Responsabilidades |
|---|---|---|
| **Product Owner** | Cristhian Rodriguez Ruiz | Prioriza el backlog, acepta o rechaza historias en la review, responde dudas de negocio en ≤ 24 h, aporta datos (catálogo, zona, textos) |
| **Scrum Master** (rotativo) | Un dev por sprint | Facilita ceremonias, retira impedimentos, cuida que las reglas del 06 se cumplan (PR pequeños, contrato-primero) |
| **Equipo de desarrollo** | Backend, Frontend, Dev 3 (Opción A) | Estima, se compromete con el Sprint Goal, revisa cruzado, entrega incrementos que cumplen la Definition of Done |

Sin gerente de proyecto: con 3–4 personas, Scrum puro es suficiente si las ceremonias se respetan.

### 5.2 Eventos

| Evento | Cuándo | Duración | Salida |
|---|---|---|---|
| **Sprint Planning** | Lunes de inicio de sprint | 2 h | Sprint Goal + historias comprometidas (≤ capacidad) + contrato OpenAPI del sprint identificado |
| **Daily** | Todos los días laborables | 15 min (async por escrito en el canal si alguien no puede) | Impedimentos visibles |
| **Refinamiento** | Miércoles de la semana 1 del sprint | 1 h | Historias del siguiente sprint con criterios claros y estimadas (DoR) |
| **Sprint Review** | Viernes de la semana 2 | 1 h | Demo del hito con datos reales al PO; historias aceptadas o devueltas; ajuste del backlog |
| **Retrospectiva** | Viernes de la semana 2, tras la review | 45 min | 1–3 mejoras concretas con responsable |
| **Revisión de contrato** | Ad hoc, al inicio de cada historia con API nueva | 20 min | PR de `openapi.yaml` aprobado por ambos lados antes de codificar |

### 5.3 Artefactos

- **Product Backlog:** `backlog.csv` importado a **Linear** (decisión del dueño, 09 §6): un Team, un Project por épica, Cycles de 2 semanas = sprints, etiquetas por módulo y MoSCoW, integración con GitHub por clave de issue y conector con Claude para preparar sprints y descomponer historias.
- **Sprint Backlog:** vista filtrada por sprint; cada historia se divide en tareas técnicas (subtareas del issue) en la planning.
- **Incremento:** lo que está en `main` al final del sprint, desplegado en staging y demostrado.
- **Burndown:** gráfico automático de GitHub Projects por puntos; se revisa en la daily del viernes.

### 5.4 Definition of Ready (una historia entra a un sprint solo si…)
- Criterios de aceptación escritos y entendidos por quien la hará.
- Contrato OpenAPI/evento identificado (o marcado "no aplica").
- Dependencias del CSV resueltas o planificadas en el mismo sprint con orden claro.
- Estimada en puntos por el equipo (no por una sola persona).
- Pantalla referenciada en el 05 si tiene UI.

### 5.5 Definition of Done (una historia está terminada solo si…)
- Código en `main` vía PR ≤ 400 líneas con revisión cruzada aprobada.
- CI verde: lint, tipos, deptrac, pruebas unitarias e integración con MySQL real, contrato verificado, cliente regenerado, Lighthouse (si toca tienda), auditorías de dependencias.
- Pruebas escritas según el 03 §8.8 (dominio unitario; infraestructura con BD real; E2E si es recorrido crítico).
- Errores devuelven Problem Details con `codigo` estable y el frontend los traduce.
- Acciones sobre pedidos, cobros, precios y configuración quedan en `sys_auditoria`.
- Textos en español de Perú, accesibles (etiquetas, foco, contraste), sin datos personales en logs.
- Desplegada en staging y demostrada al PO (o demostrable en la review).
- Documentación tocada si cambió un contrato, un evento o una decisión (ADR).

### 5.6 Métricas del proceso (se leen en cada retro)
- Velocidad (puntos hechos vs. comprometidos).
- Lead time de PR (apertura → merge; meta ≤ 24 h).
- Tasa de PR devueltos por CI rojo.
- Deuda declarada (historias con "mock" o "TODO" aceptadas).
- DORA desde S3: frecuencia de despliegue a staging, tasa de fallo de cambios, tiempo de recuperación.

## 6. Backlog por sprint (resumen; detalle en `backlog.csv`)

| Sprint | Historias | Puntos | Backend | Frontend / Dev 3 |
|---|---|---|---|---|
| S0 | E0-01…E0-10 | 35 | Shared, Identidad, ADRs, deptrac | Workspace, design system, login, cliente API |
| S1 | E1-01…E1-11 | 62 | Catálogo, Inventario, reservas concurrentes, eventos, API tienda, seeders | Tienda navegable SSR; panel catálogo e inventario |
| S2 | E2-01…E2-11 | 61 | Zonas, Clientes, crear pedido todo-o-nada, estados, consulta pública | Carrito, checkout 3 pasos, Mi pedido; panel pedidos y cliente |
| S3 | E3-01…E3-11 | 75 | Riesgo, preparación y nota, rutas, correos | Hoy, revisión, preparación, rutas con mapa, configuración |
| S4 | E4-01…E4-10 | 65 | Resultado de parada idempotente, cobros, cierre de caja | App motorizado offline completa; panel caja y devoluciones |
| S5 | E5-01…E5-12 | 67 | Reverb, avisos, seguridad, ARCO, runbooks, observabilidad | Realtime, avísame, SEO, rendimiento, cuenta, pruebas con usuarios |
| Lanz. | E6-01…E6-06 | 21 | Producción, importador CSV | Capacitación, guías, piloto |
| E7 (09) | E7-01…E7-17 | 46 (+8 en fase 1.1) | Reclamaciones, reembolsos, rate limits, Sentry, afirmaciones | Legal, cookies, analítica, accesibilidad verificable, Linear |
| **Total** | **88** | **432 en fase 1 + 8 en fase 1.1** | | |

Épicas: E0 Fundaciones · E1 Catálogo/Inventario · E2 Pedidos (Zonas, Clientes) · E3 Riesgo/Fulfillment/Notificaciones · E4 Motorizado/Cobranza · E5 Tiempo real/Calidad/Seguridad · E6 Lanzamiento.

## 7. Riesgos del plan y respuesta

| Riesgo | Prob. | Impacto | Respuesta |
|---|---|---|---|
| Alcance > capacidad (§3) | Alta si Opción C | Alto | Elegir A o B antes del día 1; congelar alcance; cambios van a fase 1.1 |
| Ausencia de un dev en un equipo de 2–3 | Media | Alto | Conocimiento repartido por revisión cruzada; documentación en repo; Sprint Goal se reduce, no se estira |
| PO no disponible para reviews | Media | Alto | 2 h semanales fijas en calendario; decisiones por escrito en el issue |
| Datos del negocio llegan tarde (catálogo, fotos, polígono) | Alta | Medio | Prerrequisitos §2.1 con fecha; seeders de ejemplo permiten avanzar |
| Offline del motorizado más complejo de lo previsto | Media | Alto | Historia E4-07 de 13 pts aislada en su librería; prueba E2E desde el inicio de S4; plan B: sincronización manual "Enviar" con reintentos |
| Cambios de versión de Angular/Laravel durante el proyecto | Baja | Bajo | Versiones fijadas en S0; actualizar solo en S5 si es menor |
| Proveedor de mapas con límites gratuitos | Media | Bajo | Adaptador intercambiable; captura manual de coordenadas como respaldo |

## 8. Fase 1.1 (mes 4, post-lanzamiento; no comprometida)

Lo que naturalmente sigue al go-live y donde caen las historias que se muevan: cuenta de cliente completa, "volver a pedir", "avísame" si quedó fuera, ficha de cliente ampliada, optimización real de rutas, reporte de ventas y márgenes, importador de existencias periódico, mejoras de las pruebas con usuarios, y la preparación de la fase 2 (segundo distrito).

## 9. Lo que necesito de ti para cerrar este documento

1. **Fecha de inicio** → convierto el roadmap a fechas reales y genero el calendario de ceremonias.
2. **Opción de equipo (A, B o C)** → reasigno el backlog por sprint si es B.
3. Confirmar los prerrequisitos de §2.1 que ya tienes resueltos.
