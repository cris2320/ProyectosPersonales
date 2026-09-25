# Contratos

- `openapi.yaml` — API REST `/api/v1`. Se edita **antes** de codificar. El backend la verifica contra sus rutas (`php artisan openapi:verify`); el frontend genera `libs/api-client` (`npm run api:generate`).
- `events/*.vN.json` — esquemas JSON de los eventos publicados por el outbox (ADR-006). Cambios incompatibles = nuevo archivo `vN+1` + ADR.

Validar en local: `make contract`.
