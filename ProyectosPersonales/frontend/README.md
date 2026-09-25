# Frontend (workspace Angular, ADR-001)

Generar con la guía 06 §4. Estructura objetivo:
```
apps/tienda        SSR
apps/panel         PWA
apps/motorizado    PWA offline (libs/offline-queue)
libs/design-system tokens (05 §7), componentes accesibles
libs/api-client    GENERADO desde ../contracts/openapi.yaml — no editar a mano
libs/dominio       EstadoPedido, formatos de dinero/teléfono
libs/realtime      Echo + reconexión + respaldo por sondeo
libs/offline-queue IndexedDB, operation_id, sincronizador
```
Scripts esperados en package.json: `api:generate`, `lint`, `test`.
