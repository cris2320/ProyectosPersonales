# 10 · Auditoría del repositorio frente a la documentación

| Campo | Valor |
|---|---|
| Fecha de corte | 26/09/2026 |
| Proyecto inspeccionado | `ProyectosPersonales/`, dentro de la raíz Git `C:/Users/repre/ProyectosPersonales` |
| Base inspeccionada | Commit `a0d6ff1`; árbol inicialmente sin cambios locales |
| Responsable del proyecto | Cristhian Rodriguez Ruiz: desarrollo, pruebas y decisiones finales; 20 h/semana, confirmado el 26/09/2026 |
| Método | Lectura de los 88 archivos presentes, contraste documental, inventario, cómputos del backlog y comprobaciones estáticas |
| Límite | No se ha ejecutado la aplicación ni certificado cumplimiento funcional, de seguridad o legal |

## 1. Dictamen

**El repositorio todavía no cumple todo lo establecido en la documentación.** Es un paquete de diseño y código inicial, previo a completar el entorno de desarrollo. Esto encaja con el estado abierto de los documentos 06 y 07; no equivale a tener un e-commerce construido.

Hay 48 archivos PHP, incluidos cinco archivos de pruebas con 17 métodos de prueba; dos migraciones que declaran seis tablas; configuración Docker; OpenAPI 0.2.0 con ocho operaciones; un esquema de evento y tokens CSS. Existen Shared, Identidad y una parte del dominio/contratos de Pedidos. Los otros ocho módulos del diseño no están implementados.

Faltan `backend/composer.json`, `artisan`, `bootstrap/`, el armazón de pruebas y dependencias; también `frontend/package.json`, `angular.json`, aplicaciones y cliente generado. La ausencia de los esqueletos se reconoce expresamente en el 06. En cambio, `.github/`, `.editorconfig`, `.gitignore`, `.gitattributes`, `infra/.env.example`, `backend/.env.ci` y `backend/.env.example.dtoo` se anuncian como entregados y no están en esta copia, incluso buscando archivos ocultos.

No asigno un porcentaje de avance: contar archivos o tablas no demuestra aceptación de historias. Ninguna historia se considera terminada únicamente porque exista código.

## 2. Matriz de requisitos y evidencia

| Área / requisito | Referencia | Evidencia real | Evaluación al corte |
|---|---|---|---|
| Angular: tienda SSR, panel PWA y motorizado offline | ADR-001/008, 03 §5.3, 05 | README, configuración de cliente/Lighthouse y tokens; sin apps | Pendiente de implementación |
| Laravel API y módulos | ADR-002/005, 06/07 | Clases PHP sin proyecto instalable; ocho módulos ausentes y Pedidos parcial | Parcial, no ejecutable |
| Fronteras y dominio sin framework | ADR-005, 03 §5.2 | `deptrac.yaml` separa módulos públicos/internos, no capas Domain/Infrastructure ni SQL por prefijo | Verificación incompleta; no ejecutada |
| MySQL y modelo de datos | ADR-003, 04 | Seis tablas declaradas, no migradas en esta auditoría | Parcial; el diseño enumera 42 tablas propias contando las adendas, no 39 |
| Stock, cupos y pedido todo-o-nada | 01 §6.3, 02 §4.2–4.5 | Enum de estados y contratos de consulta; sin casos de uso ni tablas de estos módulos | Pendiente |
| Cobros inmutables y conciliación | 02 §4.8, 04 §8 | Sin módulo Cobranza | Pendiente |
| Outbox y consumidores idempotentes | ADR-006, 07 | Publicador exige transacción; consumidor registra evento; dispatcher y pruebas parciales | Parcial; A05/A06 |
| Idempotencia de escrituras | 03 §8.4, ADR-006 | Middleware y tres pruebas secuenciales | No garantiza concurrencia ni recuperación tras fallo; A03 |
| Login, MFA y autorización | 01 §6.4, 07 | Casos de uso, rutas, cinco pruebas | Parcial; configuración y controles pendientes, A04/A07 |
| Errores RFC 9457 | 03 §8.3, OpenAPI | Constructor y manejador presentes | Parcial; transición y cabeceras incompletas, A08 |
| Contrato primero y cliente generado | ADR-006, 06 §5 | OpenAPI de ocho operaciones; solo cinco rutas de auth presentes | Contrato inicial, sin verificador ni cliente generado; A09 |
| Auditoría y configuración | 04 §11, 07 | Clases y seeders presentes | Parcial; atomicidad, redacción y validación pendientes, A10 |
| Tiempo real ≤ 2 s | ADR-007, 01 §6.2 | Servicio Reverb declarado; sin canales ni consumidores de negocio | Pendiente; scheduler de outbox por minuto no basta |
| Accesibilidad y rendimiento | 01 §6.2/6.6, 05/09 | Tokens y Lighthouse configurado sin UI/build | No verificable en pantallas; contraste de éxito falla, A11 |
| Pipeline, revisión y despliegue | ADR-004, 06 | Makefile; no workflows | Ausente en copia local; protección remota no verificada |
| Backups, PITR, RPO/RTO y alertas | 01 §6.1, 03 §7/8.5 | Docker local sin mecanismos ni ensayos | Pendiente |
| Privacidad, reclamaciones, reembolsos y terceros | 09, adenda 04 §16c | Requisitos y backlog; sin textos finales ni flujos | Borrador; A12 y decisiones del 11 |
| Pruebas de usuarios, concurrencia y offline | 03 §8.8, 05 §9, 09 §5 | Solo pruebas iniciales PHP | Sin evidencia de ejecución o aceptación |

## 3. Hallazgos priorizados

Prioridades: **P0** bloquea construir sobre una base fiable o comprometer el plan; **P1** debe corregirse antes de aceptar el incremento afectado; **P2** debe aclararse antes de desarrollar esa parte. La prioridad describe el riesgo del proyecto, no una vulnerabilidad explotada en producción.

### A01 · P0 · Arranque y CI anunciados sin archivos suficientes

Evidencia: [guía 06](06-repositorio-y-pipeline.md) §1/§3/§4, [README](../README.md), [Compose](../infra/docker-compose.yml). `make up` requiere archivos de entorno ausentes; PHP requiere `artisan`; el frontend ejecuta `npm ci` sin manifiesto ni lockfile. Faltan los workflows anunciados.

Además, la raíz Git está un nivel por encima del proyecto: un workflow dentro de `ProyectosPersonales/.github/workflows` no quedaría en la ubicación raíz esperada. Hay que decidir si se conserva la carpeta y se usan rutas de trabajo explícitas o si se reorganiza el repositorio. No se ha movido código.

Cierre: clon limpio → instalación reproducible → servicios sanos → tres apps compiladas → pruebas y CI ejecutados. Fijar versiones y lockfiles; los comandos `latest` de la guía no fijan una base reproducible.

### A02 · P0 · Capacidad incompatible con el alcance

[Backlog](backlog.csv): 88 historias, 440 puntos totales: 87 historias/432 puntos de fase 1 y una historia/8 puntos de fase 1.1. Los Must suman 415 y los Should 17. La cifra de 71 historias del antiguo 08 estaba desactualizada.

El antiguo plan afirmaba que tres desarrolladores con ~270 puntos podían completar 415 Must. Faltaban 145 puntos incluso eliminando todos los Should. La opción de dos desarrolladores en 20 semanas tampoco se justificaba con su propia velocidad. Ahora el equipo confirmado es una persona; el [08 actualizado](08-roadmap-y-plan-scrum.md) sustituye esas promesas por capacidad explícita y una propuesta de primer trimestre.

### A03 · P0 · La idempotencia no protege dos solicitudes simultáneas

Evidencia: [IdempotencyKey.php](../backend/app/Shared/Idempotency/IdempotencyKey.php), líneas 29–50. Se consulta la clave, se ejecuta `$next` y solo después se guarda la respuesta con `insertOrIgnore`. Dos solicitudes pueden no encontrar fila y ejecutar ambas la operación; ignorar el segundo registro no revierte el segundo efecto de negocio. Un fallo tras guardar el pedido y antes de guardar la respuesta también deja abierta la repetición.

La consulta no compara método/ruta ni comprueba `expira_en`; una misma clave/cuerpo puede recuperar una respuesta de otro endpoint o una respuesta vencida hasta la limpieza. El actor invitado depende de IP: cambiar de Wi-Fi a datos cambia la identidad del reintento. El replay fuerza `application/json`, perdiendo el tipo Problem Details y cabeceras relevantes.

Cierre: diseñar adquisición atómica y alcance de clave/actor/operación, transacción o recuperación coherente con el efecto de negocio, expiración y replay íntegro. Probar concurrencia real MySQL, corte tras commit, cambio de red, ruta distinta, clave vencida y respuestas de error. No basta repetir secuencialmente el test actual.

### A04 · P0 · La guía de Sanctum puede introducir recursión

El [07 §2 paso 5](07-desarrollo-bloque-1-shared-identidad.md) ordena incluir `personal` en `sanctum.guard`, mientras [IdentidadServiceProvider](../backend/app/Modules/Identidad/IdentidadServiceProvider.php), línea 20, define ese guard con driver `sanctum`. El guard de Sanctum consulta los guards de esa lista; incluirse a sí mismo introduce una llamada recursiva.

Es una inferencia del código y de la guía, contrastada con el [código oficial de Sanctum 4.x](https://raw.githubusercontent.com/laravel/sanctum/4.x/src/Guard.php); no una ejecución fallida de este proyecto. No hay versión instalada. Cierre: separar guards de sesión y token según la versión fijada y probar login, acceso bearer, separación clientes/personal y denegaciones.

### A05 · P1 · Despacho de eventos distinto de las garantías documentadas

[DespacharOutbox](../backend/app/Shared/Console/DespacharOutbox.php), línea 43, usa `lockForUpdate`; `SKIP LOCKED` solo está comentado. [Compose](../infra/docker-compose.yml) no incluye el worker permanente propuesto en 07; [SharedServiceProvider](../backend/app/Shared/SharedServiceProvider.php) despacha cada minuto. Ese recorrido puede exceder ampliamente los 2 s de stock en vivo.

[EventoPublicado](../backend/app/Shared/Events/EventoPublicado.php), línea 39, relanza el primer error y no continúa con los consumidores posteriores, pese a que el comentario promete independencia. Tras cinco intentos se puede agotar el job sin llegar a consumidores posteriores; filas con diez fallos de despacho quedan excluidas sin herramienta de recuperación propia.

Cierre: worker continuo, bloqueo acordado, cola `eventos` configurada, consumidores aislados o recuperación probada, alertas y replay. Ensayar caída de Redis, duplicados y fallo de un consumidor antes de aceptar las garantías del ADR-006.

### A06 · P1 · Pruebas presentes no equivalen a garantías probadas

[OutboxTest](../backend/app/Shared/Tests/Feature/OutboxTest.php) usa `RefreshDatabase` y espera que publicar «fuera de transacción» lance excepción. En el [trait oficial de Laravel 12.x](https://raw.githubusercontent.com/laravel/framework/12.x/src/Illuminate/Foundation/Testing/RefreshDatabase.php) ese mecanismo abre una transacción de prueba; el caso no crea la precondición que anuncia. Debe comprobarse contra la versión que se instale.

`LoginTest::test_ruta_protegida_por_rol` consulta `/auth/yo`, que solo exige autenticación y no el middleware `rol`; no prueba rechazo de roles. No hay pruebas de idempotencia concurrente, aislamiento por objeto, fronteras, frontend, contratos, carga ni E2E. Cierre: corregir las precondiciones y añadir pruebas de los riesgos de A03–A10 antes de cerrar el 07.

### A07 · P1 · Autorización y ciclo de MFA incompletos

[RequiereRol](../backend/app/Modules/Identidad/Http/RequiereRol.php) comprueba habilidades guardadas en el token; no comprueba `activo` ni el rol vigente. Desactivar/cambiar el rol no revoca tokens en el código presente. [ConfigurarMfa](../backend/app/Modules/Identidad/Application/ConfigurarMfa.php) permite sustituir el secreto usando un token completo, sin acreditar MFA anterior; las rutas MFA no tienen un límite específico declarado. El token temporal también puede consultar `/auth/yo`, aunque la guía dice que sirve solo para configurar MFA.

Cierre: definir autorización vigente, revocación, reautenticación para cambiar MFA, recuperación y alcance exacto del token temporal; probar los rechazos. Los fallos de código MFA tampoco registran el evento de auditoría que promete el 07.

### A08 · P1 · Errores de dominio y 429 no conservan el contrato

[TransicionInvalida](../backend/app/Modules/Pedidos/Domain/TransicionInvalida.php) hereda directamente de `DomainException`; [ManejadorDeExcepciones](../backend/app/Shared/Http/ManejadorDeExcepciones.php) solo reconoce `ExcepcionDeDominio`. Si llega al manejador actual se convierte en 500 en vez de 409 `transicion_invalida`. La rama de throttle reconstruye la respuesta sin propagar `Retry-After`, requerido en 09. Otros errores HTTP no contemplados también caerían en 500.

Cierre: mapeo explícito preservando la independencia del dominio, estado/cabeceras correctos y pruebas HTTP de cada caso.

### A09 · P1 · Contratos e instrucciones de integración incongruentes

En [OpenAPI](../contracts/openapi.yaml), líneas 34 y 98, los patrones MFA usan dos barras invertidas dentro de una cadena YAML con comillas simples. La comprobación estática muestra que no aceptan `123456`. El patrón de dinero del esquema JSON de evento sí acepta `86.00`; su escape JSON es correcto.

La cabecera del contrato exige idempotencia en toda escritura, pero auth no la declara ni la aplica: se debe definir la excepción para sesión/MFA, evitando cachear secretos por rutina. Faltan respuestas comunes 401/403/422/429 y el registro explícito de versión de términos del checkout. ADR-002 habla de generar OpenAPI desde anotaciones; ADR-006 establece contrato primero.

El ejemplo `PedidoCreado` del 07 §3 usa `$this->datos` sin declararlo y pasa un array como segundo argumento al constructor heredado, cuyo parámetro es `?string $eventId`; no es un ejemplo integrable tal cual. `make:use-case` y `openapi:verify` se anuncian pero no existen.

Cierre: una sola fuente del contrato, ejemplos ejecutables, validación de esquemas y cliente generado reproducible.

### A10 · P1 · Auditoría/configuración y entradas necesitan endurecimiento

[TraceId](../backend/app/Shared/Http/TraceId.php) acepta cualquier `X-Trace-Id`; la columna de auditoría solo admite 32 caracteres. Un valor largo puede hacer fallar escrituras auditadas. [Configuracion](../backend/app/Shared/Config/Configuracion.php) actualiza el dato y escribe la auditoría separadamente, sin transacción propia; un fallo deja el cambio sin su evidencia.

[ConfiguracionSeeder](../backend/app/Shared/Database/Seeders/ConfiguracionSeeder.php) usa `updateOrInsert`: volver a ejecutar `make migrate` con `--seed` puede restablecer decisiones de negocio existentes sin auditoría. `IniciarSesion` almacena el usuario introducido en el JSON de auditoría, y `Auditoria` no aplica redacción; la prohibición documental de datos personales no está garantizada por el helper.

Cierre: validar/generar trace ID; cambios de configuración y auditoría atómicos; seed inicial no destructivo; lista de campos permitidos/redactados. `make:module` también debe validar el nombre antes de formar rutas de escritura.

### A11 · P1 · Contraste calculado distinto de lo declarado

Recalculado con los hex de [tokens.css](../frontend/libs/design-system/tokens.css), sin redondear para decidir conformidad:

| Texto / fondo | Ratio calculado | Texto normal AA |
|---|---:|---|
| `#2B2A28` / `#FFF9EE` | 13,681 | Cumple |
| `#6B665E` / `#FFF9EE` | 5,434 | Cumple |
| `#FFFFFF` / `#1E5EFF` | 5,122 | Cumple |
| `#0F2D6B` / `#FFC53D` | 8,275 | Cumple |
| `#0F2D6B` / `#FFF9EE` | 12,461 | Cumple |
| `#FFFFFF` / `#1B8A4C` | **4,387** | **No cumple 4,5:1** |
| `#FFFFFF` / `#C0392B` | 5,438 | Cumple |
| `#FFFFFF` / `#C77700` | 3,461 | Solo usos con umbral 3:1 |

El anexo C del 09 da éxito como 4,7:1 y aprobado, lo que debe corregirse. Texto grande no significa «18 px o negrita»: el umbral corresponde a 18 pt normal o 14 pt negrita, aproximadamente 24 px y 18,67 px. [Fuente: W3C, contraste mínimo](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html).

El 03 exige ≤ 200 KB de JS inicial; 06 propone warning 350 KB/error 500 KB, y Lighthouse solo advierte por peso total a 600 000 bytes. Faltan presupuesto coherente y prueba sobre SSR real. Material/Lighthouse por sí solos no certifican toda WCAG.

### A12 · P1 · Cumplimiento todavía es una propuesta que requiere correcciones

El 09 y E7-03 fijan un recordatorio a los 20 días sin calendario ni vencimiento operativo definido. Indecopi indica respuesta en **15 días hábiles improrrogables**. El aviso debe calcularse antes de ese vencimiento con calendario aplicable, no depender de un «día 20» ambiguo. [Fuente oficial: Indecopi](https://consumidor.gob.pe/2025/11/25/tulibrodereclamaciones/).

La ventana de 48 h para reembolsos está marcada como propuesta: no debe implementarse como caducidad automática de derechos sin revisión de aplicabilidad. Tampoco se han validado integralmente las afirmaciones sobre cookies, bases legales, transferencias, retención, requisitos sanitarios ni las condiciones/precios de proveedores. Se registra esta validación pendiente, sin afirmar conformidad jurídica.

El 04 incorpora cambios del 09 todavía no aprobado (reclamaciones y reposiciones). Faltan dueño modular y contratos del prefijo `rec_`, además de compatibilidad de reposiciones sin cobro con las invariantes de Cobranza. Cierre: decisión de Cristhian, revisión pertinente y trazabilidad datos/API/UX/pruebas.

## 4. Contradicciones de diseño a resolver antes de la implementación afectada

| ID | Contradicción | Decisión pendiente |
|---|---|---|
| D-T01 | 01/03/ADR-004 describen dos desarrolladores; antiguo 08 llega a tres | Una persona confirmada; adaptar revisión/flujo sin simular aprobación independiente |
| D-T02 | 01 pide MFA para personal y menciona atención al cliente; 02/07 tienen tres roles y MFA obligatorio solo admin | Fijar matriz de roles/MFA |
| D-T03 | Reserva 30 min y revisión manual 2 h | Qué ocurre con stock/cupo al minuto 30; no confirmar una reserva perdida |
| D-T04 | 02 §4.2 consume stock al despachar; §5.1 al terminar preparación | Un único evento de consumo y su compensación |
| D-T05 | Segundo fallo → Devuelto, pero stock solo vuelve al recibirse físicamente | Distinguir devolución pendiente de recepción |
| D-T06 | Faltante permite cancelar/retirar línea; enum no permite cancelar desde preparación y total se congela | Flujo auditado de faltante, cancelación y cambios de total |
| D-T07 | 05 autocompleta nombre/dirección con solo escribir un teléfono | Exigir prueba de identidad o no revelar datos guardados; el teléfono no prueba titularidad |
| D-T08 | Stock público solo por estado (ADR-007); mock UX muestra «Últimas 3» | Unificar exposición de cantidad |
| D-T09 | 02 dice ocultar agotados; 05 dice mostrarlos al final | Aplicar decisión UX y corregir tabla de eventos |
| D-T10 | Arquitectura restringe acceso al Core a eventos; contratos existentes ofrecen consultas al Core | Precisar llamadas síncronas permitidas |
| D-T11 | `Dinero` solo acepta montos no negativos; ajustes/diferencias admiten negativos | Tipo para importes firmados y regla de redondeo; pruebas de IGV |
| D-T12 | 99,5 % en horario 14 h × 30 días se describe como ~1 h de caída | El presupuesto es 2,1 h/mes bajo ese supuesto; revisar objetivo/denominador |
| D-T13 | 05 exige validar prototipos antes de construir; backlog deja usuarios para S5 | Añadir validación temprana y repetir antes de lanzamiento |
| D-T14 | ADR-008 borra datos al logout/24 h; operaciones offline no pueden perderse | Política de retención/salida con operaciones pendientes y recuperación |
| D-T15 | ADR-008 pide renovación de token; código solo emite sesión de 12 h | Recuperación de sesión y envío de operaciones pendientes al expirar |

Las marcas «decisiones abiertas» del 02 §8 son históricas: varias se resolvieron en ADR-005/006/007/008 y en el 04. No deben confundirse con decisiones nuevas ni reabrirse todas por rutina.

## 5. Evidencia de comprobaciones y límites

| Comprobación | Resultado |
|---|---|
| Inventario con `rg --files --hidden`, excluyendo `.git` | 88 archivos previos a esta revisión; 48 PHP |
| `git status --short` inicial | Limpio |
| Lectura de documentos, ocho ADRs, contratos, infraestructura y código | Completada; hallazgos con archivos de referencia |
| `Import-Csv` y suma por prioridad/sprint | 88 IDs únicos; 440 puntos; ninguna referencia de dependencia inexistente |
| Esquema JSON del evento | JSON parseable; patrón de total acepta `86.00` |
| Patrones MFA extraídos de YAML | `123456` no coincide; comprobación puntual, no validación completa OpenAPI |
| Contraste sRGB de ocho pares | Seis cumplen para texto normal; éxito y alerta no llegan a 4,5:1 |
| Conteo del modelo de datos | 41 encabezados de tablas + `rec_reclamaciones` en adenda = 42; excluye tablas del framework |
| Calendario | 26/09/2026 sábado; 12 semanas inclusivas terminan 18/12; aniversario de tres meses 26/12 |
| Herramientas en PATH de esta sesión | No se encontraron PHP, Composer, Node, npm, Docker ni make; Python aparece como alias Windows, no se utilizó |
| PHPUnit/Pest, MySQL, build, Lighthouse, deptrac, CI remoto | **No ejecutados**: faltan proyecto/dependencias y herramientas en esta sesión |
| GitHub, Linear, hosting, registros y decisiones externas | No inspeccionados ni modificados; no se infiere su estado desde la copia local |

Verificación de los archivos de planificación generados: se conservaron los 88 IDs, criterios, puntos y dependencias originales; diez historias E0 propuestas suman 35 puntos. Las 16 tareas suman 180 h (140 de historias + 32 de revisión/correcciones + 8 de aceptación), sin exceder tres horas enfocadas por día ni quince por semana. Los enlaces locales de los informes/índice y `git diff --check` se comprobaron sin errores. Estas comprobaciones de documentación no sustituyen pruebas de aplicación.

El siguiente trabajo está desglosado en el [roadmap actualizado](08-roadmap-y-plan-scrum.md) y el [registro de decisiones](11-decisiones-pendientes.md). Esta entrega modifica documentación y planificación; los defectos de aplicación descritos siguen pendientes de corregir.
