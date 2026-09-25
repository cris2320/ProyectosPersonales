# ADR-005 · Monolito modular en Laravel con fronteras verificadas

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Desarrollador backend, dueño |
| Relacionado con | 01 §6.7 · 02 §3 (mapa de contextos) · ADR-002 |

## Contexto
El documento 02 define diez módulos con la regla de que ninguno lee las tablas de otro y cualquiera puede extraerse después. Con un desarrollador backend y ~2 000 pedidos/mes, microservicios serían un costo sin beneficio; pero un monolito sin fronteras se degrada en meses (lección de Shopify).

## Decisión
**Un solo despliegue Laravel** organizado en módulos bajo `app/Modules/`, con estructura propia (sin paquete de terceros) y fronteras verificadas por herramienta.

```
app/Modules/Pedidos/
  Domain/          → entidades, value objects (Dinero, EstadoPedido), reglas, eventos de dominio
  Application/     → casos de uso (CrearPedido, CancelarPedido), DTOs, interfaces de puertos
  Infrastructure/  → modelos Eloquent, repositorios, adaptadores a otros módulos
  Http/            → Controllers, Form Requests, API Resources, rutas   ← aquí vive el MVC
  Contracts/       → la ÚNICA interfaz pública del módulo (queries y comandos que otros pueden usar)
  Events/          → eventos publicados, versionados
  Database/        → migraciones y seeders del módulo
  Tests/
  PedidosServiceProvider.php
```

**Reglas de frontera (verificadas en CI con deptrac o equivalente):**
1. Un módulo solo puede importar de otro módulo lo que está en `Contracts/` y `Events/`.
2. Prohibido: relaciones Eloquent entre modelos de módulos distintos, consultas a tablas con prefijo de otro módulo, claves foráneas entre módulos (ADR-003).
3. `Domain/` no depende de Laravel ni de Eloquent: es PHP puro y se prueba sin base de datos.
4. Los controladores solo validan, invocan un caso de uso y devuelven un Resource. Cero reglas de negocio en `Http/`.
5. Cada módulo tiene su ServiceProvider que registra rutas, bindings y listeners; se puede desactivar sin romper el arranque.

**Módulos fase 1:** Catalogo, Inventario, Zonas, Clientes, Pedidos, Riesgo, Fulfillment, Cobranza, Identidad, Notificaciones (uno a uno con el documento 02). `Identidad` y `Notificaciones` usan componentes nativos de Laravel y son deliberadamente delgados.

**Datos compartidos a propósito:** el pedido copia precio, nombre y dirección al crearse (documento 02 §3.3). Nunca se hace JOIN entre módulos; si un panel necesita datos de varios, se compone en la capa de aplicación o con un modelo de lectura propio.

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| Microservicios desde el inicio | Diez despliegues, red, observabilidad distribuida y consistencia eventual para un equipo de uno. Costo desproporcionado. |
| Laravel estándar (`app/Models`, `app/Http` planos) | Es el camino más rápido la primera semana y el más caro el primer año; sin fronteras, Eloquent invita a acoplar todo. |
| Paquete `nwidart/laravel-modules` | Resuelve la estructura, pero añade una dependencia y convenciones propias; con namespaces y un ServiceProvider por módulo se logra lo mismo con Laravel puro. Puede adoptarse después sin cambiar la lógica. |
| Arquitectura hexagonal estricta con puertos para todo | Demasiada ceremonia para módulos delgados; se aplica completa solo en los módulos core (Pedidos, Fulfillment, Cobranza, Riesgo) y simplificada en los de soporte. |

## Consecuencias
**Positivas:** un despliegue, una base, transacciones locales simples; las fronteras existen desde el primer commit y las verifica el CI, no la disciplina; extraer un módulo es mover una carpeta y cambiar el adaptador.

**Negativas / riesgos:** más archivos por funcionalidad que el Laravel clásico → se generan con comandos artisan propios (`make:module`, `make:use-case`); la tentación de saltarse la regla "por una vez" → deptrac en CI falla el PR.

**Revisar si:** un módulo necesita escalar o desplegarse independientemente (p. ej. Fulfillment con optimización de rutas); entonces se extrae detrás de su `Contracts/` y los eventos siguen fluyendo por el outbox (ADR-006).
