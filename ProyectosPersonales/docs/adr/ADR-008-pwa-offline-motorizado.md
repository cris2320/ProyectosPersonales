# ADR-008 · PWA con operación fuera de línea para el motorizado

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Equipo de desarrollo, dueño |
| Relacionado con | 01 §4.1, §6.1, §6.6 · 02 §4.7, §4.8 · ADR-001 · ADR-006 (Idempotency-Key) |

## Contexto
Los motorizados registran entregas, rechazos y cobros en la calle, con cobertura móvil intermitente. Ninguna operación puede perderse (un cobro no registrado es dinero sin rastro) y ningún bloqueo del sistema puede detenerlos (documento 02 §9). El dueño confirmó que la app es web.

## Decisión
La app del motorizado es una **Progressive Web App** en Angular (`apps/motorizado`, ADR-001) con estas capacidades:

**Instalación y acceso a hardware**
- Manifest + service worker de Angular: ícono en pantalla de inicio, pantalla completa, arranque sin conexión.
- Geolocalización del navegador para registrar dónde se marcó cada parada; cámara (input file) para foto opcional de entrega.

**Modelo fuera de línea: cola local de operaciones**
1. Al iniciar la ruta con conexión, la app descarga y guarda en **IndexedDB** la ruta completa: paradas, direcciones, coordenadas, montos a cobrar, teléfono del cliente.
2. Cada acción del motorizado (entregado, rechazado, cobro con método y monto, nota) se guarda **primero localmente** como una operación con `operation_id` ULID generado en el dispositivo, hora local y coordenadas. La interfaz responde al instante.
3. Un sincronizador envía las operaciones pendientes en orden cuando hay red, con `Idempotency-Key = operation_id` (ADR-006). Si la respuesta se pierde, el reintento es seguro.
4. Estados visibles: *guardado en el teléfono* → *enviado* → *confirmado por el servidor*. El motorizado siempre ve cuántas operaciones faltan por sincronizar.
5. Al cerrar la ruta, la app exige que todo esté confirmado o muestra claramente qué falta; **no bloquea** al motorizado, pero avisa al administrador (documento 02 §9).

**Resolución de conflictos**
- Las operaciones son **hechos añadidos** (append-only), no ediciones: dos registros sobre la misma parada se conservan ambos y el servidor aplica la máquina de estados; si una transición no es válida (p. ej. entregar una parada ya cancelada por el administrador), el servidor la rechaza con motivo y la app la muestra para que el administrador resuelva.
- La **hora de negocio** es la del dispositivo al registrar; la del servidor se guarda aparte para auditoría.
- El servidor manda en los datos maestros: si el administrador cambió la ruta mientras el motorizado estaba sin señal, al reconectar la app recibe la ruta nueva y marca las diferencias.

**Seguridad**
- Token Sanctum con expiración corta y renovación; los datos locales se borran al cerrar sesión o tras 24 h sin uso.
- IndexedDB guarda solo lo necesario para la ruta del día (mínimo de datos personales, documento 01 §6.5).

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| App nativa (Android/iOS) | Segundo código base, publicación en tiendas, un solo frontend en el equipo. |
| Capacitor sobre Angular | Válido y reutiliza el código; se reserva como paso siguiente si se necesita hardware que la web no alcance (impresoras Bluetooth, notificaciones push avanzadas). |
| PWA en línea solamente | Perdería registros en zonas sin señal; incumple el requisito de no perder cobros. |
| Sincronización de "estado completo" (subir toda la ruta) | Genera conflictos difíciles; la cola de operaciones idempotentes es el patrón probado para este caso. |

## Consecuencias
**Positivas:** ninguna entrega ni cobro se pierde por falta de señal; el motorizado nunca espera al servidor; el mismo backend sirve a la app sin código especial más allá de la idempotencia.

**Negativas / riesgos:** el fuera de línea añade complejidad al frontend → se implementa en una librería aislada (`libs/offline-queue`) con pruebas propias; la hora del dispositivo puede estar mal → se guarda también la del servidor y se alerta si difieren más de 10 min; el almacenamiento del navegador puede limpiarse → los datos se reponen desde el servidor al reconectar y las operaciones se envían tan pronto hay red.

**Revisar si:** se necesita hardware no accesible desde el navegador o notificaciones push fiables en iOS; entonces empaquetar la misma app con Capacitor.
