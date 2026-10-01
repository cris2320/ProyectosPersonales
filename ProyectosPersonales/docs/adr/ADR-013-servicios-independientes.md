# ADR-013 · Servicios con despliegue, escalado y trazabilidad independientes

| Campo | Valor |
|---|---|
| Fecha | 2026-09-30 |
| Estado | **Aceptado en dirección arquitectónica por Cristhian; distribución y herramientas propuestas, pendientes de validar** |
| Sustituye | ADR-005; ADR-006 en comunicación entre servicios y transacciones compartidas |
| Mantiene | Angular, Laravel API, MySQL y monorepo; contratos primero, idempotencia y outbox |
| Implementación | Pendiente. No existen todavía servicios ejecutables independientes |

## Contexto

Cristhian confirma: «la idea es poder separar los servicios [...] que cada servicio tenga su trazabilidad y poder también escalar». Se abandona el backend de negocio con despliegue único como arquitectura objetivo. Un único desarrollador dispone de 20 h semanales, incluidas pruebas y documentación. La separación se construirá por incrementos verificables; no se promete implementar todo el producto en tres meses.

Separar servicios permite asignar recursos y publicar versiones por capacidad de negocio. No elimina por sí mismo la sobrecarga: base de datos, gateway, broker y servicios dependientes pueden seguir siendo cuellos de botella. La capacidad se medirá, no se deducirá del número de contenedores.

## Decisión y alcance confirmado

- Servicios de negocio con API/contratos, proceso, imagen, configuración, credenciales, migraciones y pipeline de despliegue propios. Pueden permanecer en el mismo monorepo.
- Cada servicio es dueño de sus datos. Ningún otro servicio consulta sus tablas, usa sus modelos ORM ni ejecuta sus migraciones. Una instancia MySQL compartida puede alojar bases y usuarios separados en local; eso no proporciona aislamiento de recursos ni alta disponibilidad en producción.
- Escalado independiente de réplicas HTTP y consumidores. Los procesos HTTP no dependen de archivos o sesiones exclusivos de una réplica. Archivos persistentes accesibles desde todas las réplicas mediante almacenamiento compartido.
- Contratos HTTP y de eventos versionados. Sin obligación de desplegar simultáneamente todos los servicios para un cambio compatible.
- Trazas distribuidas y métricas por servicio; una operación debe poder seguirse entre HTTP, outbox y consumidores.
- Transacciones ACID dentro de cada servicio. Los flujos entre servicios tienen estados persistidos, reintentos idempotentes y compensaciones; no se simula una transacción global.

## Distribución inicial propuesta

| Servicio | Responsabilidad y propiedad de datos | Incremento |
|---|---|---|
| Identidad | Credenciales, MFA, sesiones/tokens y asignación de roles; cada servicio aplica su autorización de recursos | Primer trimestre: mínimo verificable |
| Catálogo | Productos, presentaciones, precios e imágenes; disponibilidad publicada es una proyección, no stock autoritativo | Primer trimestre: producto mínimo, sin CRUD comercial completo |
| Inventario | Existencias, movimientos y reservas; único que decide si puede reservar stock | Posterior |
| Pedidos | Pedido y sus estados; orquestación del checkout. Clientes, Zonas/cupos y Riesgo inicialmente como módulos internos con límites explícitos | Posterior; revisar esta agrupación antes del checkout |
| Fulfillment | Preparación, rutas, paradas y recepción física de devoluciones; solicita movimientos a Inventario | Posterior |
| Cobranza | Cobros, conciliación y ajustes inmutables; consume hechos de entrega | Posterior |
| Notificaciones | Correos y avisos derivados de eventos; preferencias de entrega y deduplicación | Posterior; en el trimestre solo consumidor técnico de prueba |

Los diez contextos del dominio no exigen diez servicios desde el primer día. Esta agrupación es una propuesta técnica revisable, no una aprobación de nuevos estados o reglas comerciales. Identidad no es el almacén de datos comerciales de Clientes. Reverb, SSR, gateway y broker son componentes técnicos, no servicios de negocio adicionales.

## Comunicación, consistencia y seguridad

Para cada cambio de negocio se guarda el evento en el outbox en la misma transacción local. El publicador espera confirmación del broker; cada suscriptor tiene su propia cola y registra `event_id` en su inbox en la misma transacción que sus efectos locales. Se asume entrega **al menos una vez**: duplicados y mensajes fuera de orden deben manejarse. Los efectos externos, como correo, requieren su propia estrategia de deduplicación; el inbox no garantiza exactamente una entrega externa.

El sobre del evento incluye `event_id`, tipo/versión, productor, instante UTC, identificador del agregado, versión/secuencia del agregado, `correlation_id`, `causation_id` y contexto de traza. Los contratos establecen qué hacer ante versiones desconocidas, huecos o eventos tardíos. Reintentos con espera incremental y variación aleatoria, límite de intentos, cola de fallos y replay controlado. No descartar un evento por haberse agotado sus reintentos.

HTTP tiene timeout, límite de concurrencia y presupuesto total de tiempo. Reintentar escrituras solo con contrato idempotente. Rate limiting en el gateway y controles en cada servicio; la cola tiene límites de acumulación, alarmas y control de admisión. Ante saturación se rechaza de forma explícita (429/503 según causa), sin aceptar silenciosamente trabajo que no se podrá conservar.

La autenticación entre servicios y la validación de tokens se definirán en S01/S04: emisión, audiencia, expiración, rotación, revocación y permisos. No compartir tablas Sanctum ni confiar en cabeceras de rol aportadas por el cliente. Para el primer incremento se evaluará introspección autenticada en Identidad: timeout y fallo cerrado en operaciones protegidas; caché solo con una política explícita de revocación. Los endpoints públicos de catálogo no deben necesitar Identidad para cada lectura. La decisión final queda documentada antes de implementar accesos protegidos.

### Checkout cuando se implemente

Pedidos persiste un intento y coordina la reserva en Inventario con un identificador estable. Un timeout significa resultado desconocido, no reserva rechazada: debe consultarse/reintentarse con la misma clave. Tras asegurar stock y cupo se finaliza el pedido; ante rechazo se liberan recursos mediante operaciones idempotentes. Si una liberación falla, queda pendiente de reconciliación, con alarma y recuperación manual documentada.

Debe acordarse el vencimiento de reservas y su confirmación con una transición atómica en Inventario para evitar que una reserva expire mientras se confirma. Un intento técnico pendiente no equivale a un pedido confirmado. Antes de programar checkout se actualizarán estados, contrato HTTP (incluida respuesta pendiente/consulta de estado), temporizadores y UX en 02/04/05. No se implementa esta saga durante el primer trimestre propuesto.

## Trazabilidad y aislamiento verificables

OpenTelemetry es la base propuesta: propagar contexto W3C (`traceparent`/`tracestate`) por HTTP y mensajes, crear spans por operación y correlacionar logs mediante `trace_id`, `span_id`, `service.name`, versión y entorno. `correlation_id` de negocio persiste aunque un reintento posterior origine otra traza. Validar contexto recibido; no usarlo como autorización, ni incluir tokens, teléfonos, direcciones o cuerpos completos en logs/baggage.

Por servicio: tasa de peticiones, p95/p99, errores, concurrencia, CPU/memoria, conexiones/latencia SQL. Por consumidor: edad del mensaje más antiguo, retraso del outbox, reintentos y fallos. No usar identificadores de usuario/pedido como etiquetas de métricas de alta cardinalidad. La auditoría de negocio es persistente y separada del muestreo de trazas.

## Herramientas propuestas, no instaladas

Docker Compose para desarrollo; Nginx como entrada HTTP inicial; RabbitMQ candidato para eventos entre servicios; Redis para caché/colas internas donde proceda. Horizon administra colas Redis, no se asumirá que administra RabbitMQ. OpenTelemetry Collector más un visor de trazas (Jaeger o Tempo) para el incremento; métricas y backend definitivo en ADR-010. Versiones, clientes PHP, licencias y consumo se verifican en S01/S02 antes de fijar dependencias.

No hace falta instalar Kubernetes para iniciar. Varios contenedores en una sola máquina demuestran separación de procesos, pero no independencia ante caída del host. El despliegue productivo y sus costos se resolverán en ADR-009.

## Criterios de aceptación de la arquitectura

1. Publicar Catálogo sin reconstruir/desplegar Identidad; contratos compatibles y rollback ensayado.
2. Dos réplicas de Catálogo detrás de la entrada HTTP; datos/archivos consistentes y ninguna afinidad obligatoria a una réplica.
3. La credencial SQL de Catálogo no puede leer ni escribir la base de Identidad.
4. Una operación de prueba muestra trazas HTTP entre servicios y publicación/consumo asíncrono, con productor y consumidor identificados.
5. Duplicación, caída tras commit y reentrega no duplican el efecto local del consumidor de prueba; recuperación del broker comprobada.
6. Saturar Catálogo bajo recursos limitados no impide autenticar por encima del umbral definido; fallar Identidad no interrumpe lecturas públicas. Registrar límites, carga, hardware y resultados. Una prueba en un host no acredita alta disponibilidad.
7. Contratos, fallos y seguridad pasan CI por servicio. Los objetivos de rendimiento se fijan antes de medir; no se declara capacidad comercial desde una prueba de salud.

## Consecuencias y alternativas

Se descarta continuar con el despliegue único como objetivo porque el usuario requiere independencia por servicio. También se descarta crear todos los servicios a la vez: aumentaría el trabajo sin entregar pruebas de funcionamiento. El primer trimestre cambia de fundaciones monolíticas a una base distribuida mínima de Identidad y Catálogo, con un consumidor técnico; las historias anteriores se conservan para reestimación.

La extracción de módulos del código inicial requiere adaptar persistencia, autenticación, contratos y pruebas; no consiste en mover carpetas. Se conserva ese código como referencia hasta demostrar su integración. La aprobación de esta dirección no cierra documentos de cumplimiento, decisiones de negocio ni pruebas pendientes.

## Referencias

- [Patrón Saga, Microsoft](https://learn.microsoft.com/en-us/azure/architecture/patterns/saga): transacciones locales y compensaciones.
- [Fiabilidad de RabbitMQ](https://www.rabbitmq.com/docs/reliability): confirmaciones, reentrega y duplicados.
- [Propagación de contexto, OpenTelemetry](https://opentelemetry.io/docs/concepts/context-propagation/): contexto entre procesos.

ADR-012 permanece reservado para analítica. Este ADR no selecciona ni contrata proveedores.
