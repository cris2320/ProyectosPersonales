# Registro de decisiones de arquitectura (ADR) · D'Too Limpieza

Cada decisión técnica relevante queda registrada en un archivo corto e inmutable. Si una decisión cambia, no se edita: se crea un ADR nuevo que la reemplaza y el anterior se marca como *Reemplazado*.

**Formato:** Contexto → Decisión → Alternativas consideradas → Consecuencias.
**Estados:** Propuesto · Aceptado · Reemplazado por ADR-xxx · Obsoleto.

| N.º | Título | Estado | Fecha |
|---|---|---|---|
| [ADR-001](ADR-001-angular-frontend.md) | Angular como frontend para tienda, panel y app de motorizado | Aceptado | 2026-09-14 |
| [ADR-002](ADR-002-laravel-backend.md) | Laravel como backend, exclusivamente API | Aceptado | 2026-09-14 |
| [ADR-003](ADR-003-mysql.md) | MySQL como base de datos transaccional | Aceptado | 2026-09-14 |
| [ADR-004](ADR-004-github-monorepo-flujo.md) | GitHub, monorepo y flujo de trabajo trunk-based | Aceptado | 2026-09-14 |
| [ADR-005](ADR-005-monolito-modular.md) | Monolito modular en Laravel con fronteras verificadas | Aceptado | 2026-09-14 |
| [ADR-006](ADR-006-comunicacion-entre-modulos.md) | Comunicación entre módulos: contratos síncronos y eventos vía outbox | Aceptado | 2026-09-14 |
| [ADR-007](ADR-007-tiempo-real.md) | Tiempo real con Laravel Reverb (WebSockets) | Aceptado | 2026-09-14 |
| [ADR-008](ADR-008-pwa-offline-motorizado.md) | PWA con operación fuera de línea para el motorizado | Aceptado | 2026-09-14 |

## Cómo crear un ADR

1. Copiar `plantilla.md` como `ADR-NNN-titulo-corto.md`.
2. Escribirlo en el mismo PR que introduce la decisión en el código.
3. Revisión cruzada (frontend ↔ backend). Sin ADR aprobado no se mergea un cambio estructural.
4. Añadir la fila al índice.

## Decisiones que requieren ADR

Elegir o cambiar un framework, librería central o servicio externo; cambiar la forma en que los módulos se comunican; cambiar el esquema de un evento publicado; introducir un nuevo almacén de datos; cambiar la estrategia de autenticación o de despliegue.
