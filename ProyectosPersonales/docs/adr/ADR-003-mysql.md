# ADR-003 · MySQL como base de datos transaccional

| Campo | Valor |
|---|---|
| Estado | Aceptado (reemplaza la elección inicial de SQL Server, descartada antes de codificar) |
| Fecha | 2026-09-14 |
| Decisores | Cristhian Rodriguez Ruiz (dueño), desarrollador backend |
| Relacionado con | 01 §6.1, §6.3, §6.7 · 02 §4.2, §4.3, §4.8 |

## Contexto
Dinero, stock, cupos y estados de pedido exigen transacciones ACID y bloqueos de fila fiables (documento 01 §6.3). Zonas de cobertura requieren decidir si una coordenada cae dentro de un polígono. El proyecto debe poder crecer a ~20 000 pedidos/mes y a varias zonas sin cambiar de motor.

## Decisión
**MySQL 8.x (última LTS disponible)** con InnoDB, un solo servidor al inicio, respaldos automáticos con recuperación a punto en el tiempo. Convenciones fijas:

- Codificación `utf8mb4`, collation `utf8mb4_0900_ai_ci`; zona horaria del servidor UTC, conversión a America/Lima en la aplicación.
- **Dinero:** `DECIMAL(12,2)` + columna `moneda` (`PEN`). Nunca `FLOAT`/`DOUBLE`.
- **Identificadores:** clave primaria interna `BIGINT UNSIGNED AUTO_INCREMENT`; identificador público `CHAR(26)` ULID (ordenable por tiempo, no revela volumen).
- **Fechas:** `DATETIME(3)` en UTC.
- **Estados:** `VARCHAR(32)` con validación en la aplicación (no `ENUM` de MySQL, que obliga a migrar para añadir un estado).
- **Zonas:** columna `POLYGON` con `SRID 4326` e índice espacial; pertenencia con `ST_Contains`. Coordenadas de direcciones en `POINT SRID 4326`.
- **Eventos y payloads flexibles:** `JSON` solo para datos que no se filtran ni indexan (payload del outbox, señales de riesgo).
- **Concurrencia:** `SELECT ... FOR UPDATE` en orden determinista para reservas multi-línea; `UPDATE ... WHERE disponible >= n` como guarda; `FOR UPDATE SKIP LOCKED` para trabajadores del outbox.
- **Esquemas por módulo:** prefijo de tabla por módulo (`cat_`, `inv_`, `ped_`, `ful_`, `cob_`, `zon_`, `cli_`, `rsk_`) y prohibición de claves foráneas entre módulos distintos (se guardan identificadores públicos, la integridad la garantiza el módulo dueño).
- **Migraciones:** siempre expand/contract, compatibles hacia atrás; herramienta de verificación de migraciones peligrosas en CI.

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| SQL Server (elección inicial) | Camino poco transitado en Laravel, menos paquetes y ejemplos, costo de licencia fuera de Express; el dueño la retiró. |
| PostgreSQL | Técnicamente excelente (PostGIS, tipos más ricos, `SKIP LOCKED` maduro). Se descartó por la preferencia del dueño y porque MySQL es el motor por defecto del ecosistema Laravel. Es la alternativa natural si se revisa esta decisión. |
| MariaDB | Compatible pero divergente en funciones espaciales y JSON; MySQL 8 tiene mejor soporte en Laravel. |
| Base NoSQL para el catálogo | El catálogo (~500 SKU) no lo justifica; una segunda base añadiría complejidad sin beneficio en fase 1. |

## Consecuencias
**Positivas:** motor por defecto de Laravel (mejor soporte, más ejemplos); funciones espaciales nativas resuelven las zonas sin servicio externo; hosting barato y ubicuo.

**Negativas / riesgos:** `SKIP LOCKED` y funciones espaciales exigen MySQL ≥ 8.0 → se fija la versión mínima en el `docker-compose` y en la documentación de despliegue; sin `ENUM` la validación de estados vive en el código → cubierta por pruebas de la máquina de estados.

**Revisar si:** se necesitan consultas espaciales complejas (rutas óptimas, isócronas) o particionado avanzado; entonces PostgreSQL/PostGIS sería la migración lógica, viable porque el acceso a datos evita funciones propietarias fuera de las listadas.
