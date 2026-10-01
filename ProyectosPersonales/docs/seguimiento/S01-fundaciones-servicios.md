# S01 · Límites, contratos y aceptación del primer incremento

| Campo | Valor |
|---|---|
| Plan | 30/09–05/10/2026 · 12 h estimadas |
| Inicio real | 30/09/2026, America/Lima |
| Estado principal | **En curso** |
| Alcance de este archivo | Diseño inicial para implementación; sin servicios ejecutables ni pruebas de aplicación |
| Dependencia | [ADR-013](../adr/ADR-013-servicios-independientes.md), dirección confirmada |

## 1. Evidencia por criterio

| Parte | Entregable | Estado | Fecha real de entrega | Evidencia / pendiente |
|---|---|---|---|---|
| S01.1 | Mapa de propietarios y límites del incremento | **Completado: diseño documental** | 30/09/2026 | §2; no significa bases creadas |
| S01.2 | Primer borrador de contrato HTTP, autenticación/revocación y eventos | **Completado: borrador escrito** | 30/09/2026 | §3–5; revisión formal pendiente en S01.6 |
| S01.3 | Matriz de versiones y dependencias compatibles | En curso | — | §6: requisitos oficiales revisados; falta resolver versiones conjuntas y registrar resultado |
| S01.4 | Perfil y umbrales de carga antes de medir | **Completado: diseño documental** | 30/09/2026 | §7; son objetivos, no resultados medidos |
| S01.5 | Revisión de alcance y estimaciones | **Completado: revisión inicial** | 30/09/2026 | §8; sin horas reales atribuidas |
| S01.6 | Contratos verificables y revisión de cierre | Pendiente | — | Convertir §3–5 a OpenAPI/esquemas por servicio, validar y conciliar ADR-013; no se cierra con solo este borrador |

La división sirve para informar avance. No se asignan porcentajes de avance ni horas realizadas a partir del número de filas. El estado principal permanece abierto mientras S01.3/S01.6 estén pendientes.

## 2. Propiedad de datos del incremento

| Proceso | Propiedad exclusiva | Acceso permitido de otros |
|---|---|---|
| Identidad | Usuarios del personal, credenciales, secreto MFA cifrado, desafíos de alta MFA, tokens y revocaciones, auditoría de acceso | Resumen mínimo autenticado por HTTP; nunca consultar su SQL desde Catálogo |
| Catálogo | Producto mínimo (`id`, `nombre`, `descripcion`, fechas), auditoría local, idempotencia y outbox | Consulta de producto por API; evento de producto creado sin información del operador |
| Consumidor técnico de prueba | Inbox y proyección mínima del producto (`id`, `nombre`, versión) | No expone API comercial; verifica la integración de S07 |

Bases locales previstas: `dtoo_identidad`, `dtoo_catalogo`, `dtoo_prueba_eventos`, cada una con usuario de aplicación y credencial de migración acotados. La aplicación no posee permisos DDL; las pruebas verifican denegación al acceder a otra base. Una instancia MySQL compartida solo reduce recursos en local. El consumidor tendrá su propia configuración y no usará las credenciales de Catálogo.

Clientes, domicilios, reservas, pedidos, rutas y caja no se incluyen en este incremento. El Catálogo inicial no es inventario: no admite un campo de stock cuya autoridad contradiga al futuro servicio Inventario.

## 3. Contrato HTTP inicial v0.1 (borrador para formalizar)

Entrada pública del incremento: `/api/v1/identidad` y `/api/v1/catalogo`. La ruta interna de introspección no se publica en el gateway. Se conserva `contracts/openapi.yaml` como contrato anterior; no se altera retroactivamente ni se afirma que `make contract` valida los nuevos servicios.

| Método y ruta pública | Entrada | Salida correcta | Acceso |
|---|---|---|---|
| POST `/identidad/auth/login` | `usuario` (1–40), `password` (1–200), `dispositivo` (1–60), `codigo_mfa` opcional (seis dígitos) | 200: `access_token`, `token_type=Bearer`, `expires_at`, `usuario={id,nombre,rol}` | Credenciales; límite de intentos |
| GET `/identidad/auth/yo` | Bearer | 200: `id`, `nombre`, `rol`, `mfa_activo` | Token de sesión vigente; nunca token de configuración |
| POST `/identidad/auth/logout` | Bearer | 204 | Revoca sesión actual; reintento de token ya revocado puede responder 401 y el cliente igualmente elimina su copia |
| POST `/identidad/auth/mfa/generar` | Token restringido de configuración | 200: `secreto`, `otpauth_url`, `expires_at` | Solo alta inicial; secreto pendiente diez minutos |
| POST `/identidad/auth/mfa/confirmar` | Token restringido y `codigo` de seis dígitos | 200: `mfa_activo=true`; revoca tokens de configuración y exige nuevo login | Solo alta inicial |
| POST `/catalogo/productos` | Bearer, `Idempotency-Key`, `nombre` (1–160), `descripcion` opcional (0–2000) | 201 y `Location`: `id`, `nombre`, `descripcion`, `created_at`, `version=1` | Administrador con MFA y permiso `catalogo:escribir` |
| GET `/catalogo/productos/{id}` | ULID | 200: producto mínimo o 404 | Público; no consulta Identidad |

Las rutas de la tabla son relativas a `/api/v1`; se deben unir una sola vez al generar contratos. Fechas RFC 3339 UTC y ULID de 26 caracteres. Campos desconocidos en escrituras se rechazan. No se añade listado ilimitado ni subida de imágenes en S05. `nombre` no es único por sí mismo; la idempotencia evita duplicar reintentos de la misma creación.

Errores `application/problem+json`: `type`, `title`, `status`, `codigo`, `trace_id`; `detail` saneado y `errors` cuando proceda. 401 para credenciales/token inválidos; 403 para permiso insuficiente; 409 para operación idempotente aún en curso; 422 para entrada inválida o clave reutilizada con otro cuerpo; 429 con `Retry-After` al superar cuota; 503 al no poder verificar acceso o admitir trabajo de forma segura. No exponer contraseñas, secretos o detalles de dependencias.

Con credenciales válidas y administrador sin MFA: 403, código `mfa_configuracion_requerida`, con `token_configuracion` efímero; nunca una sesión de negocio. Con MFA ya configurado y código ausente/incorrecto: 401 genérico sin token. Respuestas con credenciales/MFA llevan `Cache-Control: no-store`; tokens y cuerpos quedan excluidos de logs.

Idempotencia en creación de producto: ámbito servicio/actor/método/ruta/clave, hash del cuerpo validado, efecto + outbox + respuesta persistida atómicamente, retención 24 h. Reintento idéntico devuelve el mismo resultado sin repetir efectos. Las rutas de autenticación/MFA siguen su propio protocolo; no se guardan tokens o secretos en una caché genérica de replay.

## 4. Identidad e introspección

Diseño inicial: tokens opacos cuya persistencia pertenece solo a Identidad, basados en Sanctum al integrar. Token de sesión de 12 h, según la base anterior; token de configuración de 10 min y finalidad exclusiva de alta MFA. Guardar hashes, no tokens en claro. El panel conserva el token en memoria y pide login tras recargar; no se introduce almacenamiento persistente de tokens en este incremento ni refresh token sin contrato.

Catálogo verifica escrituras mediante `POST /internal/v1/tokens/introspect` en Identidad. La credencial del servicio y el token del usuario son distintos: cabecera `Authorization: Bearer <credencial_servicio>` y cuerpo `{token, audience:"catalogo"}`. Solo el servicio autorizado puede introspectar para esa audiencia. Se autentica y limita al llamador antes de procesar el token; nada de esta ruta queda accesible al navegador.

- Credencial de servicio aleatoria, separada por entorno, mínimo 32 bytes aleatorios; solo su hash en el verificador. Rotación con dos claves identificables, ventana máxima 24 h y revocación inmediata de una comprometida. No es un token de administrador.
- TLS verificado entre servicios fuera del perfil local aislado con datos sintéticos; ningún secreto en argumentos de comandos, trazas o repositorio.
- Token válido: 200 `{active:true, sub, audience:"catalogo", permissions:["catalogo:escribir"], mfa_verified:true, expires_at}`. La respuesta no contiene nombre, teléfono ni correo.
- Token inválido, expirado, revocado, de alta MFA o de audiencia no permitida: 200 `{active:false}`. Credencial del servicio inválida: 401. El permiso se calcula desde el rol/estado actual, no solo desde habilidades emitidas tiempo atrás.
- No hay caché de autorización en el primer incremento. Timeout total de introspección: 500 ms, sin reintento automático dentro de la petición. Catálogo responde 503 si no puede verificar acceso y 401/403 según token/permisos cuando sí obtiene respuesta.
- Desactivar usuario o cambiar rol revoca sus sesiones en la misma transacción de Identidad. La siguiente introspección posterior al commit debe reflejar el cambio. Una escritura que ya obtuvo autorización puede terminar: no se promete cancelar peticiones en vuelo.
- Administrador con MFA tiene `catalogo:escribir`; almacén y motorizado no lo tienen en este incremento. Catálogo aplica además su propia policy y valida recursos.
- Alta MFA: desafío asociado a usuario y token de configuración, cinco intentos máximos, no reutilizar el mismo TOTP confirmado, confirmación atómica y revocación de tokens de configuración. Cambiar un MFA activo no se permite por estas rutas; requiere un flujo posterior con reautenticación y recuperación documentadas.

Se verificarán estas reglas contra el código inicial antes de reutilizarlo: actualmente no están garantizadas por sus guards o middleware. La introspección aquí definida es un contrato interno del proyecto, no una afirmación de conformidad OAuth/OIDC.

## 5. Eventos y broker

Selección técnica inicial: RabbitMQ con AMQP 0-9-1 y `php-amqplib/php-amqplib`, sin acoplar la mensajería entre servicios a Horizon. Falta fijar versiones compatibles y resolver dependencias (§6).

Primer evento: `catalogo.producto_creado.v1`. Sobre obligatorio: `event_id` ULID, `type`, `schema_version=1`, `producer="catalogo"`, `occurred_at` UTC, `aggregate_id`, `aggregate_version=1`, `correlation_id`, `causation_id`, contexto W3C `traceparent` y `data={id,nombre}`. `tracestate` es opcional y validado. Identificadores no secretos; nunca incluir token o datos del operador. `causation_id` identifica el comando HTTP que originó el evento. El consumidor comprueba que `data.id` coincide con `aggregate_id`.

Exchange durable tipo topic `dtoo.events`; routing key igual al tipo del evento; cola durable propia del consumidor `prueba.catalogo.producto_creado.v1`, con binding específico. Un suscriptor de negocio posterior usa otra cola; no compite por la misma cola del consumidor de prueba. Mensajes persistentes, publicación con confirms y detección de mensaje sin ruta; no marcar el outbox enviado hasta confirmar publicación válida.

Consumidor: ack después del commit del inbox y la proyección; unicidad `(consumer,event_id)`. Reintentos de fallos transitorios a 1 s, 5 s y 30 s con jitter; después cola de fallos durable, alarma y replay explícito. Un mensaje de formato/versión no admitidos se aparta directamente; no se reintenta indefinidamente. Duplicado confirmado se reconoce sin repetir efecto. Un evento distinto que reutiliza agregado/versión con contenido contradictorio queda en revisión, no sobrescribe la proyección.

El primer evento es de creación, versión 1; eventos de actualización posteriores deberán definir secuencias y recuperación de huecos antes de implementarse. Un replay conserva `event_id` y correlación; no se generan nuevos IDs para eludir la deduplicación. Si no hay capacidad en outbox/cola, rechazar escrituras antes del commit y avisar; nunca eliminar eventos confirmados silenciosamente.

## 6. Compatibilidad: evidencia y trabajo pendiente

Revisión de documentación oficial el 30/09/2026. Se distingue compatibilidad declarada de instalación/integración ejecutada.

| Componente | Requisito/evidencia | Estado |
|---|---|---|
| Laravel 13 + PHP 8.4 | [Laravel exige PHP ≥8.3](https://laravel.com/framework/docs/13.x/releases) | Base compatible por versión declarada; sin lockfile generado |
| Sanctum | [Documentación Laravel 13](https://laravel.com/framework/docs/13.x/sanctum) describe tokens y revocación | Falta resolver paquete y probar policies/guards del proyecto |
| Cliente RabbitMQ | [Manifiesto oficial](https://github.com/php-amqplib/php-amqplib/blob/master/composer.json): PHP `^7.2` o `^8.0`, extensiones sockets y mbstring | PHP 8.4 entra en el rango; `master` no fija una release. Falta elegir tag estable y resolver dependencias |
| OpenTelemetry PHP | [Guía oficial](https://opentelemetry.io/docs/languages/php/getting-started/) diferencia instrumentación manual y automática | Probar SDK/exportador OTLP en PHP 8.4; empezar manual para el recorrido mínimo; no presumir plugin Laravel compatible |
| RabbitMQ | [Guía de fiabilidad](https://www.rabbitmq.com/docs/reliability) documenta confirms y reentrega | Fijar imagen soportada y protocolo del cliente; persistencia/recuperación se prueban en S07 |
| Collector y visor | OTLP desde PHP; Jaeger como candidato local | Elegir tags y revisar protocolos/puertos antes del Compose; no hay elección de proveedor productivo |
| Angular/Node | Base candidata del [12](../12-prerrequisitos-windows.md) | Resolver parches/lockfile en su integración; el panel de S06 conserva el alcance mínimo |

En esta sesión Docker, PHP, Composer y Node no se encontraron como comandos disponibles en PATH. No demuestra su ausencia en todos los entornos. Por tanto no se ejecutó una resolución conjunta de dependencias ni un linter OpenAPI. **No se cierra S01.3 con esta tabla.**

Para terminar: disponer de entorno mínimo; seleccionar releases estables, registrar fuentes/tag/fecha, resolver una prueba de dependencias PHP (Laravel/Sanctum/cliente AMQP/OTel), ejecutar `composer validate` y `composer check-platform-reqs`, y guardar evidencia sin secretos. Los lockfiles definitivos y builds por servicio se integran en S03; la prueba de S01 no sustituye esas builds. Si la herramienta exige instalar WSL/Docker, adelantar solo ese prerrequisito de S02 y registrar su fecha real.

## 7. Perfil de aceptación para S09

Objetivos iniciales del incremento, establecidos **antes** de medir. No son capacidad prometida para producción. Registrar configuración del equipo, límites efectivos de contenedores, versiones, dataset y script. Si el equipo no puede sostener el entorno o el generador es el cuello de botella, el resultado no demuestra capacidad; documentar y replanificar el ensayo.

Dataset: 1 000 productos sintéticos para lectura y cuentas de prueba independientes. Generación de carga desde proceso separado; dos minutos de calentamiento y diez de medición por escenario. Desactivar muestreo para el recorrido de trazabilidad de aceptación; usar muestreo fijo documentado en carga para no cambiar resultados entre ensayos.

| Escenario | Carga/condición | Umbral de aceptación |
|---|---|---|
| Lectura pública | 20 peticiones/s, llegada constante, GET por IDs variados | p95 ≤300 ms, p99 ≤1 s, errores inesperados <1 %, sin iteraciones perdidas por falta de generador |
| Escritura protegida | 1 creación/s con claves distintas y administradores sintéticos | p95 ≤800 ms, errores inesperados <1 %, efecto/outbox por creación correcta |
| Login/MFA | 1 login/s con cuentas y TOTP de prueba independientes; respetar controles por cuenta | p95 ≤1 s, errores inesperados <1 %; no desactivar la protección para lograr el resultado |
| Catálogo saturado | Incrementos de 20 peticiones/s cada minuto hasta rechazo/saturación o techo 200/s; login paralelo 1/s | Login mantiene los umbrales anteriores; Catálogo limita explícitamente 429/503. Si no se logra saturación, marcar ensayo inconcluso y redefinir carga antes de repetir |
| Identidad caído | Detener Identidad; mantener GET público y probar POST protegido | Lecturas cumplen objetivo; escrituras fallan cerrado con 503 en ≤1 s y sin efectos |
| Dos réplicas | Repetir lectura con 1 y 2 réplicas, registrar recursos totales y distribución | Ambas reciben tráfico; detener una mantiene lecturas y consistencia. No exigir ni afirmar mejora lineal |
| Broker recuperado | Persistir 100 creaciones, interrumpir broker y recuperarlo dentro de capacidad del outbox | 100 efectos únicos en proyección tras recuperación; cola drenada en ≤60 s; ningún evento perdido |
| Concurrencia idempotente | 20 solicitudes simultáneas de la misma creación/clave | Un producto y un evento; reintentos convergen a misma respuesta; cuerpo distinto rechazado |

Las pruebas de errores intencionales se contabilizan por separado del error inesperado. Cuotas iniciales para el ensayo: lecturas 60/s por origen con ráfaga 120; escritura 5/s por usuario; login cinco intentos/min por cuenta y veinte/min por origen, con bloqueos temporales y límites globales configurables. El ensayo de login usa un conjunto suficiente de cuentas/orígenes controlados; no debilita cuotas para usuarios reales. La prueba de saturación distribuye carga controlada o prueba el servicio directamente en red de ensayo, conservando límites de recursos y documentando la ruta.

Las cifras se revisan antes del ensayo si cambian recursos/contrato; se conserva la versión previa y el motivo. No reducir umbrales después de un fallo para declararlo aprobado.

## 8. Estimación y secuencia revisadas

Se mantienen **12 h estimadas S01**, **168 h de tareas** y **9 h de reserva**. Se ha revisado el alcance: dos servicios mínimos, una pantalla de acceso/operación, un evento y un consumidor técnico. No se añade catálogo completo, SPA persistente/offline, recuperación MFA de producción ni saga de checkout al trimestre. Si la resolución de dependencias o la seguridad del acceso amplían trabajo, registrar desviación antes de reasignar tareas.

No hay horas de trabajo de Cristhian medidas en esta sesión. Completar documentos con asistencia no consume automáticamente las 12 h estimadas ni acredita ahorro de esa cantidad. La siguiente acción es cerrar S01.3/S01.6; S02 mantiene 06/10–12/10 como línea base salvo adelanto real registrado.
