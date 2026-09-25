# ADR-007 · Tiempo real con Laravel Reverb (WebSockets)

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Equipo de desarrollo, dueño |
| Relacionado con | 01 §4.1, §6.2 (disponibilidad en ≤ 2 s) · 02 §4.10 · ADR-006 |

## Contexto
El dueño requiere que la tienda muestre al instante cuando un producto queda agotado o vuelve a estar disponible, y el panel de operación necesita ver pedidos nuevos, cambios de estado y avisos sin recargar. Restricción: sin servicios de pago por mensaje.

## Decisión
**Laravel Reverb** (servidor WebSocket de primera parte, autoalojado, sin costo) en el backend y **Laravel Echo** en Angular como cliente. Canales:

| Canal | Público | Contenido |
|---|---|---|
| `catalogo.disponibilidad` | Tienda (público) | `sku`, estado (`disponible` / `pocas_unidades` / `agotado`). No se envía la cantidad exacta. |
| `panel.pedidos` | Panel (privado, rol almacén/administrador) | Pedido creado, cambió de estado, en revisión, faltante. |
| `panel.avisos` | Panel (privado, administrador) | Cupo agotado, diferencia de caja, cierre pendiente, revisión próxima a vencer. |
| `motorizado.{id}` | App del motorizado (privado) | Ruta asignada, parada añadida o cancelada. |

Reglas:
- Los mensajes se emiten desde los **consumidores de eventos** (ADR-006), nunca dentro de la transacción de negocio.
- El frontend trata el mensaje como **señal para actualizar**, no como fuente de verdad: al confirmar el checkout, el servidor vuelve a validar stock y cupo.
- Si el WebSocket cae, el cliente reintenta con espera exponencial y, mientras, consulta la API cada 30 s (degradación elegante).
- Reverb corre como proceso separado detrás del mismo proxy TLS; en producción se supervisa con el mismo gestor que las colas.

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| Pusher / Ably (servicios gestionados) | Cobro por conexiones y mensajes; restricción de costos del dueño. Reverb usa el mismo protocolo, así que migrar es cambiar configuración. |
| Server-Sent Events (SSE) | Suficiente para tienda (solo servidor → cliente) y más simple, pero PHP-FPM mantiene un proceso por conexión abierta, lo que no escala; Reverb resuelve eso con un servidor dedicado. |
| Sondeo (polling) cada pocos segundos | Carga innecesaria al servidor y no cumple ≤ 2 s con cientos de visitantes; se conserva solo como respaldo. |
| Mercure u otro hub externo | Otro componente que operar; Reverb es de primera parte y se configura desde Laravel. |

## Consecuencias
**Positivas:** sin costo por mensaje, integración nativa con eventos y colas de Laravel, mismo protocolo que Pusher (migración trivial si algún día se desea gestionado).

**Negativas / riesgos:** un proceso más que operar y monitorear → se incluye en `docker-compose` y en los runbooks; un canal público podría exponer datos → el canal de disponibilidad solo emite estado, jamás cantidades ni precios.

**Revisar si:** las conexiones simultáneas superan lo que una instancia de Reverb soporta cómodamente (miles); entonces escalar Reverb horizontalmente con Redis, que ya está en la pila.
