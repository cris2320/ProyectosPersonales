# 01 · Visión del producto y requisitos de calidad

| Campo | Valor |
|---|---|
| Proyecto | **D'Too Limpieza** — e-commerce de productos de limpieza, aseo y desinfección |
| Estado | **v1.1 — CERRADO. Aprobado por el dueño del producto el 2026-09-14** |
| Fecha | 2026-09-11 |
| Dueño del producto | Cristhian Rodriguez Ruiz |
| Siguiente revisión | Al cerrar el paso 4 (arquitectura) o ante cambios de alcance |

> Convención: **[SUPUESTO]** = decisión tomada por el equipo pendiente de confirmar. **[PENDIENTE]** = dato que falta. Los bloques ya confirmados no llevan marca.

---

## 1. Problema y oportunidad

Los hogares y pequeños negocios de Carmen de la Legua-Reynoso (Callao) compran productos de limpieza de forma recurrente, en pequeñas cantidades y con poca planificación: el detergente se acaba, el negocio necesita desinfectante antes de abrir. Hoy esa compra implica desplazarse a un mercado o bodega, cargar productos pesados o voluminosos y pagar en efectivo.

La oportunidad es ofrecer **compra en línea con pago en puerta** — el medio de pago en el que este cliente ya confía — con **entrega en el mismo día** dentro del distrito, operada con almacén y motorizados propios.

El diferencial competitivo es la combinación de cercanía (entrega rápida a nivel distrital), pago contraentrega sin fricción y un catálogo especializado con presentaciones para hogar y para negocio (formatos grandes, packs).

## 2. Visión

> D'Too Limpieza es la forma más simple y confiable de abastecer un hogar o un negocio de productos de limpieza en el distrito: pides en minutos, te llega hoy, pagas cuando lo recibes.

## 3. Clientes y segmentos

| Segmento | Descripción | Necesidad principal | Objetivo de negocio |
|---|---|---|---|
| Hogar (B2C) | Familias del distrito, compra recurrente de reposición | Rapidez, precio claro, pagar en efectivo o Yape/Plin al recibir | ~40 % de los pedidos (~800/mes en fase 1) |
| Negocio (B2B) | Bodegas, restaurantes, colegios, consultorios, oficinas pequeñas | Formatos grandes, pedido repetido, horario de entrega | ~60 % de los pedidos (~1 200/mes en fase 1) |

### 3.1 Dimensionamiento de la operación

**Capacidad de entrega fase 1 (dato confirmado: 3 motorizados propios)**

| Variable | Valor | Base |
|---|---|---|
| Motorizados | 3 | Confirmado |
| Entregas por motorizado y turno | 15–20 | Distrito pequeño y denso (~2 km de extremo a extremo), rutas con varios pedidos, tiempo extra por cobro y vuelto en puerta |
| Capacidad diaria | **~60 pedidos/día** (pico 75–80 con rutas densas) | 3 × 20 |
| Días operativos al mes | 30 | Confirmado: lunes a domingo |
| Horario de operación | 7:00 a 21:00 (14 h) | Confirmado |
| Salidas (rutas) por motorizado y día | 3 | Simulado: mañana, mediodía y tarde |
| Pedidos por salida | 6–7 en promedio | 20 entregas / 3 salidas |
| Capacidad física por moto | ~30 pedidos | Aproximado; no es el límite, el límite es el tiempo |
| Capacidad mensual | **~1 800–2 100 pedidos/mes** (hasta ~2 400 en un mes excelente) | 60–70 × 30 |

**Meta de negocio fase 1:** ~2 000 pedidos/mes, repartidos aproximadamente 40 % hogar / 60 % negocio.

> Nota sobre el horario: 14 horas de operación con 3 motorizados implica turnos escalonados (por ejemplo, dos en la mañana y uno en la tarde, o turnos de 8 h con solapamiento). El cupo diario y las ventanas de entrega deben reflejar cuántos motorizados hay activos en cada franja, no solo el total.

**Aspiración de largo plazo:** ~20 000 pedidos/mes (8 000 hogar + 12 000 negocio). Alcanzarla implica una flota de 35–45 motorizados y varios turnos de almacén; la arquitectura debe soportarla sin rediseño (§6.2 y §6.7), pero ninguna métrica de fase 1 se mide contra este número.

**Implicación de producto:** el checkout debe conocer el cupo diario de entregas. Cuando el día está lleno, ofrece la siguiente ventana disponible (mañana) en lugar de prometer una entrega que la flota no puede cumplir. El cupo es un dato configurable por zona y por día.

En la fase 1 ambos segmentos comparten el mismo catálogo y el mismo checkout; el B2B se diferencia por presentaciones y precios por volumen. Cuentas empresa con crédito o pedidos programados quedan para fases posteriores.

## 4. Alcance

### 4.1 Fase 1 (lanzamiento)

**Incluido**
- Catálogo con categorías, presentaciones y stock en tiempo real. Tamaño de referencia para diseño: **~500 SKU y ~20 tipos de presentación** (dato de trabajo, actualizable).
- Sin monto mínimo de pedido y sin costo de envío en fase 1 (ambos configurables para fases futuras).
- Ventanas de entrega dentro del horario 7:00–21:00; entrega el mismo día sujeta al cupo disponible.
- Carrito y checkout como invitado o con cuenta, con dirección validada dentro de la zona de cobertura.
- Pago exclusivamente contraentrega: efectivo, Yape/Plin o POS del motorizado.
- Confirmación del **pedido** antes del despacho (el pago ocurre en la puerta y es un concepto distinto), **sin servicios de mensajería de pago**:
  - **Manual:** el administrador puede confirmar o rechazar cualquier pedido con un botón desde el panel. Un interruptor global permite exigir confirmación manual para todos los pedidos (recomendado las primeras semanas, mientras se calibran las reglas).
  - **Automática por historial:** el sistema reconoce a los clientes fieles por sus datos reales — entregas exitosas previas, sin rechazos, monto similar a sus compras anteriores — y los confirma sin intervención. Las reglas y sus pesos son configurables por el administrador.
  - **Verificación de correo** (código o enlace) cuando el cliente lo proporcionó; el correo es opcional.
  - **Llamada telefónica** del equipo (o WhatsApp Business gratuito, manual) para pedidos marcados como riesgo: cliente nuevo con monto alto, dirección con rechazos previos, teléfono repetido en varias cuentas, cliente sin correo y sin historial.
- Panel de operación: preparación de pedidos, asignación a motorizados, registro de entrega y cobro.
- Conciliación diaria del efectivo recaudado por motorizado.
- Seguimiento del pedido por el cliente (estado y hora estimada).
- Devoluciones y rechazos en puerta registrados con motivo.
- Resumen del pedido disponible por tres vías: correo (si lo proporcionó), consulta en la web con número de pedido + teléfono (sin cuenta), y **hoja impresa** generada desde el panel que viaja con el paquete.
- **Disponibilidad en tiempo real:** la tienda refleja al instante, sin recargar, cuando un producto pasa a disponible, pocas unidades o agotado; el checkout vuelve a validar el stock al confirmar.

**Excluido en fase 1**
- Pagos en línea (tarjeta, billeteras) — decisión de producto, no técnica.
- Emisión de comprobantes fiscales de cualquier tipo.
- Cobertura fuera del distrito.
- Marketplace de terceros, suscripciones, programa de puntos.
- Aplicación móvil nativa. La tienda es web responsive; la **app del motorizado y el panel de operación son aplicaciones web progresivas (PWA)**: se instalan en el celular desde el navegador, abren a pantalla completa, usan GPS y cámara y funcionan sin conexión. No requieren tiendas de aplicaciones ni un segundo código base.

### 4.2 Fases previstas (para diseñar hoy sin construir)

- **Fase 2:** ampliar a distritos vecinos de Callao; zonas de cobertura y tarifas por zona; couriers externos con recaudación como complemento.
- **Fase 3:** cuentas empresa con pedidos recurrentes, listas de precios por cliente, pedidos programados.
- **Fase 4:** medios de pago digitales previos a la entrega, si el negocio lo justifica.

**Implicación arquitectónica:** desde la fase 1 todo pedido lleva zona de entrega, todo precio lleva lista de precios, y el cobro es un evento de la entrega, no del checkout. Así las fases 2 a 4 son configuración y nuevos módulos, no reescritura.

## 5. Métricas de éxito

| Métrica | Definición | Objetivo fase 1 |
|---|---|---|
| Pedidos entregados y cobrados / pedidos confirmados | Tasa de éxito de la operación contraentrega | ≥ 92 % |
| Tasa de rechazo en puerta | Pedidos rechazados o cliente no encontrado / despachados | ≤ 5 % |
| Tiempo pedido → entrega | Mediana, dentro del distrito, en horario de operación | ≤ 4 h |
| Conversión checkout | Pedidos confirmados / checkouts iniciados | ≥ 45 % |
| Recompra a 30 días | Clientes con ≥ 2 pedidos en 30 días | ≥ 30 % |
| Diferencia de caja | Efectivo declarado vs. esperado por motorizado y día | 0 sin justificar |
| Pedido perfecto | A tiempo, completo, sin daño, resumen correcto | ≥ 95 % |
| Pedidos confirmados automáticamente | Sin intervención humana / total confirmados | ≥ 80 % |

## 6. Requisitos de calidad (atributos no funcionales)

Escenarios medibles. Gobiernan las decisiones de arquitectura y se verifican con pruebas antes de cada lanzamiento.

### 6.1 Disponibilidad y continuidad
- La tienda debe estar disponible el **99,5 %** del tiempo en horario comercial (≈ 1 h de caída al mes). Objetivo fase 2: 99,9 %.
- Si la web pública cae, la operación en curso no se detiene: el panel de despacho y la app del motorizado (PWA) deben poder consultar y actualizar los pedidos ya confirmados. La app del motorizado trabaja **fuera de línea**: guarda entregas y cobros en el dispositivo y los sincroniza al recuperar señal; el diseño de esa sincronización se define en el paso 4.
- Recuperación: RPO ≤ 15 min, RTO ≤ 2 h, con restauración probada trimestralmente.

### 6.2 Rendimiento y capacidad
- Página de producto y categoría: LCP ≤ 2,5 s, INP ≤ 200 ms, CLS ≤ 0,1 en móvil 4G (percentil 75).
- Checkout: cada paso responde en ≤ 500 ms (p95) del lado servidor.
- Capacidad operativa fase 1: **~60 pedidos/día** (§3.1). Capacidad técnica de diseño: **300 pedidos/día** con picos de **60 pedidos/hora**, para que el sistema no sea el límite al sumar motorizados. La arquitectura debe soportar **10× (3 000 pedidos/día)** solo añadiendo recursos, sin cambio estructural, y la aspiración de 20 000/mes sin rediseño.
- Panel de operación y app del motorizado: cada acción (asignar, marcar entregado, registrar cobro) responde en ≤ 300 ms (p95); 10 operadores concurrentes en fase 1, diseñado para 50.
- Cupo de entregas por día y zona configurable; el checkout consulta el cupo disponible antes de ofrecer una ventana de entrega.
- Cambios de disponibilidad de stock visibles en la tienda en ≤ 2 s desde que ocurren en almacén o por otra venta (conexión en tiempo real servidor → navegador; herramienta a decidir en el paso 3).

### 6.3 Integridad de datos y dinero
- Nunca se sobrevende: dos clientes no pueden reservar la última unidad. Verificado con prueba de concurrencia automatizada.
- Todo cobro registrado por un motorizado queda inmutable con hora, monto, método y quién lo registró; las correcciones se hacen con asientos compensatorios, nunca editando.
- El cierre de caja diario cuadra pedido a pedido; una diferencia bloquea el cierre hasta justificarse.
- Montos en decimal exacto con moneda (PEN); IGV calculado y almacenado por línea.

### 6.4 Seguridad
- Estándar de referencia: OWASP ASVS 5.0 nivel 2. Sin alcance PCI DSS (no se procesan tarjetas).
- Autenticación de personal con MFA; roles separados para administración, almacén, motorizado y atención al cliente; principio de mínimo privilegio.
- Protección contra pedidos fraudulentos: historial del cliente por teléfono, verificación de correo cuando existe, límites de monto para clientes nuevos, lista de direcciones y teléfonos con rechazos previos, confirmación manual para pedidos de riesgo o para todos si el administrador lo activa.
- Registro auditable de cambios de estado de pedidos y cobros.

### 6.5 Protección de datos personales
- Cumplimiento de la Ley 29733 y su Reglamento (D.S. 016-2024-JUS): registro del banco de datos ante la ANPD, política de privacidad, consentimiento separado para marketing, canal de derechos ARCO, protocolo de notificación de incidentes en 48 h.
- Minimización: el checkout pide solo nombre, **teléfono (obligatorio, identificador principal del cliente)**, correo (opcional), dirección y referencia.
- Retención: los datos de contacto de pedidos de invitados se anonimizan a los **12 meses** de la entrega; los de clientes registrados, a los **24 meses** de inactividad.

### 6.6 Usabilidad y accesibilidad
- Diseño mobile-first; el checkout completo en ≤ 3 pantallas para invitados.
- Cumplimiento WCAG 2.2 AA en catálogo y checkout, verificado con herramientas automáticas y una sesión con usuario real.
- Lenguaje claro en español peruano; el total a pagar en puerta visible antes de confirmar.
- La app del motorizado se opera con una mano, en exterior y con conexión intermitente.

### 6.7 Escalabilidad y evolución
- Arquitectura de monolito modular en Laravel: módulos con fronteras explícitas (Catálogo, Pedidos, Fulfillment, Cobranza, Clientes, Zonas). Un módulo no accede a las tablas de otro.
- Zonas de cobertura, tarifas de envío, listas de precios y métodos de cobro son datos configurables, no código.
- Cualquier módulo puede extraerse a un servicio independiente sin cambiar el contrato que expone a los demás.

### 6.8 Operabilidad
- Despliegue sin caída de servicio (migraciones expand/contract, despliegue por promoción con rollback).
- Observabilidad: logs estructurados sin datos personales, métricas de negocio (pedidos por estado, rechazos, caja) y alertas accionables.
- Cualquier miembro del equipo puede levantar el entorno completo en local con un comando.

## 7. Restricciones

| Tipo | Restricción | Origen |
|---|---|---|
| Tecnológica | Frontend Angular; backend Laravel (PHP); base de datos **MySQL**; patrón MVC dentro de cada módulo | Decisión del dueño del producto — se documenta en ADR-001 a ADR-003 |
| Negocio | Solo pago contraentrega en fase 1 | Decisión de producto |
| Negocio | Sin servicios de mensajería de pago (WhatsApp API, SMS) | Decisión de costos |
| Operativa | Un almacén, motorizados propios, un distrito | Capacidad inicial |
| Legal | Ley 29733 y Reglamento | Marco peruano |
| Equipo de operación | 2 personas de almacén (preparan y despachan), 3 motorizados (entregan y cobran), 1 administrador: Cristhian Rodriguez Ruiz (catálogo, pedidos de riesgo, cierre de caja) | Confirmado |
| Equipo de desarrollo | 2 personas: 1 frontend (Angular), 1 backend (Laravel) | Confirmado. Implica: contratos de API definidos antes de codificar, revisión cruzada de PRs, y automatización fuerte para compensar el tamaño del equipo |

## 8. Riesgos principales

| Riesgo | Impacto | Mitigación prevista |
|---|---|---|
| Alta tasa de rechazo en puerta | Costo doble de envío + producto inmovilizado | Confirmación previa por reglas, scoring, límites a clientes nuevos, métricas diarias |
| Descuadre de caja o pérdida de efectivo | Pérdida directa y de confianza interna | Registro inmutable de cobros, cierre diario obligatorio, conciliación por motorizado |
| Capacidad de entrega limitada (3 motorizados, ~60 pedidos/día) | Promesas de entrega incumplidas si la demanda supera el cupo | Cupo diario en el checkout, ventanas de entrega, métricas de saturación para decidir cuándo contratar |
| Dependencia de 3 personas para toda la entrega | Una ausencia reduce la capacidad un 33 % | Rutas reasignables desde el panel, cupo ajustable el mismo día, procedimiento de contingencia |
| Direcciones incompletas o ambiguas | Entregas fallidas | Captura con mapa y coordenadas, referencia obligatoria, validación de zona |
| Confirmación automática demasiado permisiva o demasiado estricta | Fraude o pérdida de ventas | Reglas configurables, umbrales revisados semanalmente con datos reales |
| Envío gratis sin monto mínimo con pedidos pequeños | Cada entrega tiene costo fijo (tiempo de motorizado); pedidos de bajo valor pueden no cubrirlo | Métrica de valor promedio por pedido desde el día 1; monto mínimo y tarifa de envío ya configurables para activarse cuando el negocio lo decida |
| Turnos escalonados con solo 3 motorizados en 14 h | Franjas con un solo motorizado activo; retrasos si hay picos | Cupo por franja horaria, no solo por día; visibilidad de motorizados activos en el panel |

## 9. Glosario inicial

- **Pedido:** compromiso comercial del cliente; nace en el checkout.
- **Confirmación:** validación (automática o humana) de que el pedido es real y entregable antes de preparar.
- **Despacho:** salida del pedido del almacén asignado a un motorizado.
- **Entrega:** momento en que el cliente recibe el pedido y paga; genera el cobro.
- **Cobro:** registro del dinero recibido, con método (efectivo, Yape/Plin, POS) y responsable.
- **Rechazo:** el cliente no acepta o no se encuentra; el pedido vuelve al almacén.
- **Cierre de caja:** conciliación diaria de cobros esperados vs. declarados por motorizado.
- **Zona de cobertura:** área geográfica servible con su tarifa y promesa de entrega.
- **Ruta:** conjunto de pedidos asignados a un motorizado en una salida.

## 10. Parámetros operativos confirmados (resumen)

| Parámetro | Valor |
|---|---|
| Días de operación | Lunes a domingo |
| Horario | 7:00 – 21:00 |
| Motorizados | 3, varios pedidos por ruta, ~3 salidas/día (simulado) |
| Personal de almacén | 2 |
| Catálogo de referencia | ~500 SKU, ~20 tipos de presentación |
| Monto mínimo | Ninguno |
| Costo de envío | Ninguno |
| Confirmación automática | ≥ 80 % de los pedidos |
| Retención de datos | 12 meses invitados / 24 meses inactividad |
| Correo del cliente | Opcional; el teléfono identifica al cliente |
| App motorizado y panel | PWA (web instalable, funciona sin conexión) |
| Confirmación de pedidos | Manual por botón siempre disponible; automática por historial; interruptor "manual para todos" |

## 11. Historial de versiones

| Versión | Fecha | Cambio |
|---|---|---|
| 0.1 | 2026-09-11 | Borrador inicial con supuestos |
| 0.2 | 2026-09-11 | Nombre, dueño, MySQL, sin mensajería de pago, sin integración fiscal |
| 0.3 | 2026-09-11 | Dimensionamiento ajustado a 3 motorizados |
| 0.4 | 2026-09-11 | Parámetros operativos confirmados |
| 1.0 | 2026-09-11 | Equipo confirmado; documento aprobado |
| 1.1 | 2026-09-14 | Confirmación manual por botón, correo opcional, hoja impresa, PWA para motorizado y panel, stock en tiempo real |
