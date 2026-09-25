# ADR-006 · Comunicación entre módulos: contratos síncronos y eventos vía outbox

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Equipo de desarrollo |
| Relacionado con | 02 §3.3, §6 (catálogo de eventos) · ADR-005 · ADR-003 |

## Contexto
El documento 02 distingue dos tipos de interacción: **preguntas y órdenes inmediatas** (Pedidos pide a Inventario reservar y necesita saber ahora si pudo) y **reacciones a hechos pasados** (Riesgo evalúa un `PedidoCreado`, Notificaciones envía un correo). Además, el frontend necesita un contrato estable de la API que el equipo de dos pueda acordar antes de codificar.

## Decisión

### 1. Entre módulos, dentro del backend
- **Síncrono, por interfaz de `Contracts/`**, cuando el llamador necesita la respuesta para continuar: `Inventario::reservar()`, `Zonas::tomarCupo()`, `Catalogo::precioVigente()`. La llamada ocurre dentro de la misma transacción de base de datos cuando la operación debe ser todo-o-nada (crear pedido = reservar + tomar cupo + insertar pedido).
- **Asíncrono, por evento de dominio**, para toda reacción que no bloquea al emisor: evaluación de riesgo, notificaciones, actualización de listas de observación, proyecciones para paneles.

### 2. Eventos con outbox transaccional
- Al confirmar la transacción de negocio, el evento se inserta en la tabla `outbox` **en la misma transacción**. Nunca se publica un evento cuya transacción pudo fallar.
- Un trabajador (`queue:work`) lee el outbox con `FOR UPDATE SKIP LOCKED`, publica el evento a la cola de Laravel (Redis) y lo marca como despachado.
- Cada consumidor es **idempotente**: guarda los `event_id` procesados y descarta repetidos.
- Esquema del evento: `event_id` (ULID), `nombre`, `version`, `ocurrido_en`, `agregado_id`, `payload` JSON. Los esquemas viven en `contracts/events/` y cambiar uno de forma incompatible exige nueva versión y ADR.
- Los eventos que llegan al frontend en tiempo real (ADR-007) se emiten desde los consumidores, no desde la transacción.

### 3. Entre backend y frontend
- **Contrato OpenAPI 3.1 primero:** `contracts/openapi.yaml` es la fuente de verdad. El backend lo valida en CI contra sus rutas; el frontend genera de él su cliente TypeScript (`libs/api-client`). Un cambio de contrato que rompa al frontend falla el build.
- API REST versionada por prefijo (`/api/v1`), JSON, errores en formato *problem details* (RFC 9457), paginación por cursor, identificadores públicos ULID.
- Toda operación que crea o modifica acepta `Idempotency-Key`; el servidor devuelve la misma respuesta ante reintentos (imprescindible para la PWA fuera de línea, ADR-008).

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| Eventos síncronos de Laravel sin outbox | Simple, pero si el listener falla o el proceso muere tras el commit, el evento se pierde; inaceptable para riesgo y cobros. |
| Publicar directo a la cola dentro de la transacción | Si la transacción hace rollback, el evento ya salió (el clásico *dual write*). El outbox lo elimina. |
| Broker dedicado (RabbitMQ, Kafka) desde el inicio | Volumen de fase 1 no lo justifica; Redis + colas de Laravel bastan y el outbox permite cambiar el transporte después sin tocar los módulos. |
| GraphQL | Ventajas en clientes muy variados; aquí hay tres frontends controlados por el mismo equipo y REST + OpenAPI genera tipos igual de bien con menos complejidad en el servidor. |
| Que el frontend lea la API sin contrato formal | Con dos personas trabajando en paralelo, el contrato es lo que permite avanzar sin esperar al otro. |

## Consecuencias
**Positivas:** ningún evento se pierde ni se publica en falso; los módulos no se conocen entre sí más que por contratos; el frontend puede desarrollarse contra el contrato con un servidor simulado antes de que el backend exista.

**Negativas / riesgos:** el outbox introduce un pequeño retraso (segundos) → aceptable para todas las reacciones del 02; la idempotencia obliga a una tabla de eventos procesados por consumidor → se genera con el comando `make:module`.

**Revisar si:** el volumen de eventos supera lo que un trabajador procesa con retraso ≤ 5 s; entonces escalar trabajadores o cambiar el transporte, sin cambiar los módulos.
