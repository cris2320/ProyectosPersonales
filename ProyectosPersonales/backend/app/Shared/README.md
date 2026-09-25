# Shared

Lo que no pertenece a ningún módulo (03 §5.2):
- `Money/Dinero` — value object exacto (hecho).
- `Events/` — bus con outbox transaccional, `EventoDeDominio` base, `PublicadorOutbox` (worker con `FOR UPDATE SKIP LOCKED`), `ConsumidorIdempotente` (tabla `sys_eventos_procesados`). Paso 7.
- `Idempotency/` — middleware `Idempotency-Key` sobre `sys_idempotencia`. Paso 7.
- `Http/` — `ProblemDetails` (RFC 9457), paginación por cursor. Paso 7.
- `Console/` — `make:module`, `make:use-case`, `openapi:verify`. Paso 7.
- `Audit/` — `Auditoria::registrar()` para `sys_auditoria`. Paso 7.

Regla: Shared no depende de ningún módulo.
