# 09 · Cumplimiento, accesibilidad, calidad y operación

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Estado | Borrador v0.1 — para aprobación del dueño |
| Fecha | 2026-09-25 |
| Depende de | 01, 03 §8, 04, 05 §7, 08 |
| Modifica | `backlog.csv` (historias nuevas E7-xx), 04 (adenda 16c), 08 (§3 capacidad) |

> Este documento responde a la lista de requisitos de cumplimiento y calidad del dueño del 2026-09-25. Para cada ítem: **dónde ya estaba cubierto** en la documentación aprobada, **qué se añade** ahora y **qué historia del backlog lo implementa**. Nada aquí es una intención: todo termina en una historia con criterios de aceptación o en una regla de la Definition of Done.
>
> Aviso: describo obligaciones legales peruanas según su conocimiento público; no sustituye la revisión de un abogado, que el 08 §2.3 ya recomienda antes del lanzamiento.

---

## 1. Legal y transparencia (Perú)

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Política de privacidad** | 01 §6.5, 03 §8.2, 08 §1.2 | Texto completo conforme a Ley 29733 y Reglamento: identidad del titular del banco de datos, finalidades separadas (ejecutar el pedido / marketing / analítica), plazos de retención (12/24 meses), encargados de tratamiento (VPS, correo, mapas, monitoreo), derechos ARCO y canal, transferencias internacionales si el hosting está fuera de Perú. Enlazada en pie de página, en cada formulario y en el checkout. Versionada: cada cambio crea versión nueva y se registra en `cli_consentimientos.version_texto`. | E7-01 |
| **Términos y condiciones** | 05 §3.6 (enlace en checkout) | Texto que cubre: quién vende (datos del negocio), qué se vende, cómo se forma el precio (IGV incluido, sin envío, sin mínimo), zona y horario de entrega, pago en puerta y qué pasa si el cliente no paga o no está (1 reintento, luego devolución), cancelación (antes de preparación), devoluciones y reembolsos (§ siguiente), confirmación de pedidos (llamada), edad mínima (18), ley y jurisdicción aplicable (Perú). Aceptación explícita al confirmar el pedido ("Al pedir aceptas…" con enlace) y registro en `cli_consentimientos` con finalidad `terminos`. | E7-01 |
| **Política de reembolsos y devoluciones** | 02 §4.7 (rechazo en puerta), 05 §3.8 | Política escrita y flujo en el sistema para el caso que faltaba: **producto ya entregado y cobrado** con defecto, error de producto o daño. Regla propuesta (a confirmar): reclamo hasta 48 h después de la entrega; el motorizado recoge el producto en la siguiente ruta y entrega el cambio o el dinero en efectivo; en el sistema es un pedido de tipo `reposicion` (sin cobro) o un `AjusteDeCobro` negativo con motivo `reembolso`, ambos auditados y visibles en el cierre de caja. Sin reembolsos por "cambio de opinión" tras la entrega en productos de higiene abiertos, salvo lo que exija la ley. | E7-02 |
| **Leyes locales** | 01 §7 (Ley 29733) | Se nombran explícitamente las demás normas que aplican a una tienda en línea en Perú y qué exige cada una en el sistema: **Código de Protección y Defensa del Consumidor (Ley 29571)**: precio total visible antes de comprar, información veraz, atención de reclamos; **Libro de Reclamaciones virtual (D.S. 011-2011-PCM y modificatorias)**: obligatorio en tiendas en línea, con formulario accesible desde todas las páginas, numeración correlativa, copia al consumidor y respuesta en el plazo legal; **normas de publicidad (D.L. 1044)** sobre afirmaciones; **Ley 29733** protección de datos; **normas sanitarias** sobre productos de limpieza y desinfectantes (registro sanitario o notificación DIGESA según producto, que debe figurar en la ficha cuando aplique). | E7-03 (Libro de Reclamaciones), E7-04 (fichas) |
| **Datos del negocio** | — | Bloque visible en el pie de la tienda y en Términos: razón social o nombre comercial, RUC, dirección del almacén, teléfono, correo, horario de atención, enlace al Libro de Reclamaciones. Se guardan en `sys_configuracion` (`negocio.*`) y se muestran en la nota de pedido y en los correos. **[PENDIENTE del dueño: RUC, razón social, dirección]** | E7-05 |
| **Afirmaciones sin respaldo** | — | Regla editorial y técnica: la ficha de producto solo puede afirmar lo que el fabricante declara en la etiqueta o en su ficha técnica. Cada afirmación de eficacia ("elimina el 99,9 % de bacterias", "desinfecta", "hipoalergénico") se guarda con su **fuente** (`cat_productos.afirmaciones` JSON: texto + fuente + URL o documento). El panel exige fuente para publicar un producto con afirmaciones marcadas; sin fuente, el texto no se publica. Prohibido en descripciones generadas: superlativos sin base ("el mejor"), comparaciones con marcas, promesas de salud. | E7-04 |
| **Copyright de imágenes** | 04 `cat_imagenes.alt` | Solo se publican imágenes con derecho de uso: fotos propias, fotos cedidas por el proveedor o distribuidor (con autorización escrita guardada), o bancos con licencia. Se añade `cat_imagenes.origen` (`propia` / `proveedor` / `licencia`) y `cat_imagenes.licencia_ref` (referencia del permiso). El panel no publica un producto cuya imagen no tenga origen declarado. Nunca se descargan imágenes de otras tiendas o de buscadores. | E7-06 |
| **Eliminar reseñas falsas** | — (fase 1 no tiene reseñas) | Decisión: **sin reseñas en fase 1** (evita el problema de raíz y no bloquea el lanzamiento). Se documenta desde ya la política para fase 1.1: solo pueden opinar clientes con pedido `Cobrado` de ese SKU (reseña verificada), una por pedido, moderación previa por el administrador, motivo de rechazo registrado, prohibición de incentivos por reseña, y un mecanismo para que cualquiera denuncie una reseña. Las reseñas se guardarán con `pedido_uid` para poder auditar su origen. | E7-07 (fase 1.1) |

## 2. Privacidad en la práctica

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Solo datos necesarios** | 01 §6.5 (minimización), 04 §4 | Inventario de datos personales por formulario con la justificación de cada campo (anexo A). Regla en la DoD: un campo nuevo que capture datos personales requiere justificar su finalidad en el PR. Sin DNI, sin fecha de nacimiento, sin género; correo opcional; foto de entrega opcional y sin rostros (instrucción en la app). | E7-08 |
| **Consentimiento en formularios** | 05 §3.6 (checkbox separado de marketing), 04 `cli_consentimientos` | Aplicado a **todos** los formularios que captan datos: checkout (términos obligatorio + marketing opcional), "Avísame cuando haya stock" (finalidad única), lead fuera de zona (finalidad única), crear cuenta, Libro de Reclamaciones. Cada uno con texto corto de finalidad, casilla no premarcada cuando es opcional, versión del texto y registro. | E7-08 |
| **Política de cookies** y **consentimiento de cookies** | — | La tienda usa solo cookies estrictamente necesarias (sesión del carrito, CSRF, preferencia de tema) que **no requieren consentimiento** pero sí información. La analítica se hace con una herramienta **sin cookies y sin identificadores personales** (§4), por lo que no exige banner de consentimiento. Aun así se publica una Política de cookies (qué se usa y para qué) y se muestra un **aviso informativo** discreto, no un banner bloqueante. Si en el futuro se añade cualquier cookie de terceros (píxeles publicitarios), se activa un gestor de consentimiento que bloquee esos scripts hasta aceptar. | E7-09 |
| **Seguimiento de analítica** | 05 §8 (métricas UX), 03 §8.5 | Decisión: analítica de producto **autoalojada y sin cookies** (Umami o Plausible en el mismo VPS, ADR-012). Se miden vistas, embudo de checkout por paso, abandono, conversión, búsquedas sin resultado y "pin fuera de zona", sin IP en claro ni identificadores por persona. Los eventos de negocio (pedidos, rechazos, caja) salen de la base de datos, no de la analítica. Sin Google Analytics ni píxeles en fase 1. | E7-10 |

## 3. Accesibilidad y formularios

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Sitio accesible** (WCAG 2.2 AA) | 01 §6.6, 05 §7 | Se convierte en verificación automática y manual: `axe` en cada component test, Lighthouse accesibilidad ≥ 95 en CI, y una **auditoría manual por sprint** con la lista del anexo B (teclado, lector de pantalla NVDA/TalkBack, zoom 200 %, sin color como único medio). Declaración de accesibilidad publicada en el pie. | E7-11 |
| **Formularios por teclado** | 05 §7 (navegación por teclado completa) | Criterio explícito en la DoD: todo formulario se completa y envía solo con teclado; orden de tabulación lógico; foco visible; el error lleva el foco al primer campo inválido; los diálogos atrapan y devuelven el foco; sin trampas de foco en autocompletado de dirección y en el mapa (alternativa: escribir la dirección y confirmar sin arrastrar el pin). Prueba E2E de checkout **solo con teclado**. | E7-11 |
| **Etiquetas claras** | 05 §6 (microcopy) | Cada campo tiene `<label>` visible (no solo placeholder), texto de ayuda cuando el formato importa ("9 dígitos, sin +51"), mensajes de error específicos asociados por `aria-describedby`, y nombres de botones que dicen la acción ("Confirmar pedido", no "Enviar"). Revisión de textos por el dueño antes del piloto (E5-10). | E7-11 |
| **Texto alternativo** | 04 `cat_imagenes.alt` obligatorio, 05 §7 | Guía para escribir `alt` (qué es el producto y su presentación, sin "imagen de"); imágenes decorativas con `alt=""`; validación en el panel que impide subir sin `alt`; el importador CSV exige la columna. | E7-06 |
| **Contraste de colores** | 05 §7 (≥ 4,5:1 verificado) | Tabla de pares texto/fondo con su ratio medido (anexo C) en el design system; prueba automática de contraste en los tokens (falla el build si un par cae de 4,5:1 o 3:1 para texto grande); modo alto contraste de la app del motorizado verificado al sol en E5-10. | E7-11 |

## 4. Integraciones de terceros y control de costos

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Integraciones de terceros** | 03 §3 (correo, mapas, GitHub), 03 §8.1 (sin scripts de terceros en checkout) | Inventario único de terceros (anexo D) con: para qué se usa, qué datos recibe, si es encargado de tratamiento (contrato o términos aceptados), costo y plan, límite de uso, dueño de la cuenta, y cómo se reemplaza. Regla: ningún script externo en la tienda salvo el mapa; ningún tercero nuevo sin fila en el anexo y sin ADR si toca datos personales. | E7-12 |
| **Servicio que avise del error antes que el cliente** | 03 §8.5 (alertas), ADR-010 pendiente | Decisión: **Sentry** (plan gratuito) en backend (Laravel) y en las tres apps Angular, con `trace_id` compartido, alertas a tu correo y al canal del equipo por error nuevo o pico, y **release tracking** ligado a cada despliegue para saber qué versión lo introdujo. Se suma **monitor de disponibilidad externo** (p. ej. UptimeRobot, gratuito) que avisa por correo/SMS gratuito si la tienda o la API no responden. Los datos personales se filtran antes de enviar a Sentry (sin cuerpo de peticiones con teléfono o dirección). | E7-13 (adelanta parte de E5-12) |
| **Alertas y límites de gasto en cada servicio** | — | Por cada servicio del anexo D: presupuesto mensual definido por el dueño, **alerta al 50 % y 80 %** y **límite duro** donde el proveedor lo permita (VPS: alerta de facturación; Cloudflare: plan gratuito sin cobro; Sentry: cuota del plan gratuito con corte automático, no cobra; correo: plan con tope de envíos; mapas: cuota diaria con clave restringida por dominio; GitHub: minutos de Actions con alerta). Revisión de costos en la retro de cada sprint. **[PENDIENTE del dueño: presupuesto mensual total de infraestructura]** | E7-14 |
| **Límites de peticiones por usuario o IP** | 03 §8.1 (rate limiting en login, checkout y consulta), Identidad `throttle:10,1` | Tabla explícita (anexo E) por endpoint: login personal 10/min por IP; login cliente 10/min; crear pedido 5/min por IP y 20/h por teléfono; consulta de pedido 20/min por IP; "avísame" y leads 5/min; API pública de catálogo 120/min por IP; endpoints del panel 300/min por usuario; parada del motorizado 60/min por usuario. Respuesta 429 con `Retry-After` en formato Problem Details. Además, rate limit a nivel Nginx (y CDN si se activa) como primera capa. | E7-15 |

## 5. Pruebas

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Pruebas de componentes, integración y de principio a fin** | 03 §8.8, 08 §5.5 DoD | Se fijan las herramientas y los umbrales: **componentes** (Angular + Testing Library + `axe`, en cada componente del design system y cada pantalla con lógica); **integración** (Pest con MySQL real en CI para casos de uso, reservas concurrentes, outbox, idempotencia, rate limits; contrato OpenAPI verificado); **E2E** (Playwright sobre staging con los 6 recorridos del 03 §8.8 más el checkout solo con teclado y el flujo del Libro de Reclamaciones). Cobertura mínima: dominio 90 %, aplicación 80 %; se mide en CI y se reporta en la review. | E7-16 |

## 6. Gestión del trabajo con IA

| Ítem | Ya cubierto en | Qué se añade | Historia |
|---|---|---|---|
| **Organizarse en Linear y conectarlo con la IA** | 08 §5.3 proponía GitHub Projects | Decisión: **Linear** como herramienta de backlog y sprints (reemplaza a GitHub Projects en el 08). Estructura: un *Team* "D'Too", un *Project* por épica (E0–E7), *Cycles* de 2 semanas alineados a los sprints, etiquetas por módulo y por MoSCoW, estimación en puntos. Integración con GitHub: las ramas y PRs se enlazan al issue por su clave (`DTO-123`) y el issue avanza solo al mergear. Conexión con Claude mediante el conector oficial de Linear: desde esta conversación se pueden crear y actualizar issues, preparar los sprints, redactar criterios de aceptación y descomponer historias en tareas técnicas; los desarrolladores pueden usar Claude Code con el mismo conector para tomar un issue, implementarlo y abrir el PR. El backlog.csv se importa a Linear como carga inicial. | E7-17 |

## 7. Otros riesgos (se añaden al 01 §8 y al 08 §7)

| Riesgo | Respuesta |
|---|---|
| Suplantación de la tienda (páginas o cuentas falsas que piden pagos por adelantado usando el nombre D'Too) | La tienda declara en todas las páginas y correos: "Nunca pedimos pagos por adelantado"; registro de la marca en INDECOPI (recomendado); dominio y redes oficiales listados en Datos del negocio |
| Fraude interno de caja (motorizado que registra un método distinto al real) | Conciliación por método en el cierre; Yape/Plin/tarjeta verificables con n.º de operación; muestreo aleatorio de llamadas a clientes por el administrador |
| Dependencia de un solo proveedor (VPS, correo, mapas) | Anexo D con plan de reemplazo por servicio; respaldos exportables; adaptadores intercambiables |
| Manejo de productos químicos en la entrega (derrames, etiquetado) | Ficha con advertencias del fabricante visibles; instrucción de embalaje en la nota de pedido para productos marcados como "líquido"; fuera del alcance del software salvo mostrar la información |
| Menores de edad comprando | Términos con edad mínima 18; el motorizado entrega solo a un adulto (instrucción en la app) |
| Reclamos sin respuesta en plazo legal | Libro de Reclamaciones con aviso al administrador y recordatorio automático a los 20 días |
| Pérdida de acceso a cuentas de servicios (correo del dueño comprometido) | 2FA en todas las cuentas de proveedores; gestor de contraseñas compartido del equipo; recuperación documentada en el runbook |

## 8. Impacto en el plan (08)

Historias nuevas E7-01 a E7-17: **+46 puntos en fase 1** (+8 de E7-07 en fase 1.1; detalle en `backlog.csv`). La mayoría son de Sprint 5 y la semana de lanzamiento; tres se adelantan a S0–S2 porque condicionan el diseño (inventario de datos, rate limits, Sentry). Con la Opción A el plan pasa a **432 puntos en fase 1 (415 Must) frente a ~270 de capacidad en 12 semanas**; el margen desaparece y hay que **decidir en la planning del S0** cuáles de las siguientes van a la fase 1.1 sin comprometer el lanzamiento legal: E7-07 (reseñas, ya en 1.1), E5-09 (cuenta de cliente), E5-04 (avísame), E2-11 (ficha cliente). Lo legal (E7-01 a E7-05, E7-08, E7-09) **no se mueve**: sin eso no se puede lanzar.

## 9. Pendientes del dueño

1. RUC, razón social o nombre comercial, dirección del almacén y teléfono de atención (§1 Datos del negocio).
2. Confirmar la regla de reembolsos propuesta (48 h, recogida y cambio o efectivo).
3. Presupuesto mensual total de infraestructura para fijar las alertas (§4).
4. Nombre de tu espacio de trabajo en Linear (o crearlo) para importar el backlog.

---

## Anexo A · Inventario de datos personales por formulario

| Formulario | Campo | Obligatorio | Finalidad | Retención |
|---|---|---|---|---|
| Checkout · datos | Teléfono | Sí | Identificar al cliente, confirmar y coordinar la entrega | 12 m invitado / 24 m inactividad |
| Checkout · datos | Nombre | Sí | Entrega y trato | Igual |
| Checkout · datos | Correo | No | Enviar resumen y verificación | Igual |
| Checkout · datos | Nombre del negocio | No | Entrega en B2B | Igual |
| Checkout · entrega | Dirección, referencia, coordenadas | Sí | Entregar y validar zona | Igual |
| Checkout · pago | Método y "pago con" | Sí | Preparar vuelto | Igual (dato del pedido) |
| Checkout · marketing | Consentimiento | No (no premarcado) | Ofertas | Hasta revocación |
| Avísame stock | Teléfono, correo (opcional) | Teléfono | Avisar reposición | Hasta avisar o 6 m |
| Fuera de zona | Teléfono | Sí | Avisar apertura de zona | 12 m |
| Crear cuenta | Contraseña | Sí | Acceso | Mientras exista la cuenta |
| Libro de Reclamaciones | Nombre, DNI/CE, domicilio, teléfono, correo, detalle | Según norma | Atender el reclamo (obligación legal) | Plazo legal de conservación |
| App motorizado | Coordenadas y hora de registro, foto opcional | Coordenadas | Prueba de entrega y auditoría | 12 m |

Campos que **no** se piden en ningún formulario: DNI (salvo Libro de Reclamaciones), fecha de nacimiento, género, datos de tarjeta.

## Anexo B · Lista de auditoría manual de accesibilidad (por sprint)

1. Recorrer la pantalla completa solo con Tab/Shift+Tab/Enter/Espacio/Escape; nada inaccesible, nada atrapado.
2. Foco visible en todo elemento interactivo.
3. Lector de pantalla (NVDA en PC, TalkBack en Android): cada control anuncia nombre, rol y estado.
4. Zoom 200 % y ancho 320 px: sin pérdida de contenido ni scroll horizontal.
5. Sin información transmitida solo por color (estados de pedido llevan icono + texto).
6. Objetivos táctiles ≥ 44 px (tienda) y ≥ 56 px (motorizado).
7. Errores de formulario anunciados y enlazados al campo.
8. Contenido que se mueve o actualiza en vivo (badges, avisos) usa `aria-live` sin robar el foco.

## Anexo C · Contraste de la paleta (05 §7), ratios calculados

| Par (texto sobre fondo) | Ratio | Uso | AA |
|---|---|---|---|
| Texto `#2B2A28` sobre crema `#FFF9EE` | 14,1:1 | Cuerpo | ✓ |
| Texto secundario `#6B665E` sobre crema `#FFF9EE` | 5,1:1 | Ayudas | ✓ |
| Blanco `#FFFFFF` sobre azul `#1E5EFF` | 4,6:1 | Botón primario | ✓ (justo; no usar texto < 14 px) |
| Azul oscuro `#0F2D6B` sobre amarillo `#FFC53D` | 8,3:1 | Botón "Pedir" | ✓ |
| Azul oscuro `#0F2D6B` sobre crema `#FFF9EE` | 12,8:1 | Títulos | ✓ |
| Blanco sobre éxito `#1B8A4C` | 4,7:1 | Chip entregado | ✓ |
| Blanco sobre error `#C0392B` | 5,4:1 | Chip fallido | ✓ |
| Blanco sobre alerta `#C77700` | 3,5:1 | Solo texto grande/ícono | ✓ (≥ 18 px o negrita) |

Los ratios se verifican automáticamente en CI (E7-11); si un token cambia, el build falla al bajar del umbral.

## Anexo D · Inventario de terceros (a completar en ADR-009/010/011)

| Servicio | Uso | Datos que recibe | Encargado de tratamiento | Plan / costo | Límite y alerta | Reemplazo |
|---|---|---|---|---|---|---|
| VPS (por elegir) | Toda la plataforma | Todos (en tu servidor) | Sí (contrato/términos) | US$ 20–40/mes | Alerta de facturación 50/80 % | Otro VPS: imagen Docker + restauración |
| GitHub | Código, CI | Código, sin datos de clientes | No | Gratis / Team | Minutos de Actions | GitLab |
| Sentry | Errores | Trazas sin PII (filtrado) | Sí | Gratis (cuota) | Corte automático al agotar cuota | GlitchTip autoalojado |
| UptimeRobot | Disponibilidad | URL pública | No | Gratis | — | Otro monitor |
| Correo transaccional (por elegir) | Correos a clientes con correo | Correo, nombre, pedido | Sí | Plan con tope | Tope de envíos | Otro proveedor SMTP |
| Mapas (por elegir; preferencia OSM/MapLibre) | Mapa y geocodificación | Coordenadas, texto de dirección | Sí si geocodifica | Gratis con cuota | Clave restringida por dominio; cuota diaria | Otro proveedor de tiles |
| Umami/Plausible (autoalojado) | Analítica sin cookies | Sin datos personales | No (en tu servidor) | Gratis | — | — |
| Cloudflare (opcional) | TLS, CDN, anti-bots | Tráfico | Sí | Gratis | Sin cobro en plan gratuito | Nginx + Let's Encrypt |
| Linear | Backlog | Sin datos de clientes | No | Gratis hasta límite / Standard | — | GitHub Projects |

## Anexo E · Límites de peticiones (rate limits)

| Endpoint | Límite | Clave | Capa |
|---|---|---|---|
| `POST /auth/login` (personal) | 10/min, bloqueo 15 min tras 20 fallos | IP + usuario | Laravel |
| `POST /clientes/login` | 10/min | IP + teléfono | Laravel |
| `POST /pedidos` | 5/min por IP; 20/h por teléfono | IP, teléfono | Laravel |
| `GET /pedidos/consulta` | 20/min | IP | Laravel |
| `POST /catalogo/avisame`, `POST /leads` | 5/min | IP | Laravel |
| `GET /catalogo/*` | 120/min | IP | Laravel + Nginx |
| `POST /reclamaciones` | 3/min | IP | Laravel |
| Panel (`auth:personal`) | 300/min | usuario | Laravel |
| Motorizado: resultado de parada | 60/min | usuario | Laravel |
| Global Nginx | 30 req/s por IP con ráfaga 60 | IP | Nginx |

Todos devuelven `429` en Problem Details con `Retry-After`.
