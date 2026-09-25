# ADR-002 · Laravel como backend, exclusivamente API

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Cristhian Rodriguez Ruiz (dueño), desarrollador backend |
| Relacionado con | 01 §6.3, §6.4, §6.7 · 02 completo |

## Contexto
El backend concentra todas las reglas de negocio (reservas, cupos, riesgo, estados, caja). Debe exponer una API para tres frontends Angular, ejecutar trabajos asíncronos (expiraciones, correos, eventos), emitir tiempo real y ser mantenible por un desarrollador. El dueño fijó Laravel y el patrón MVC.

## Decisión
Laravel, versión estable vigente al crear el repositorio, sobre la versión de PHP que recomiende esa release, **como API pura**: sin vistas Blade para usuarios finales; Angular renderiza todo. Componentes de Laravel que se adoptan como estándar del proyecto:

| Necesidad | Componente |
|---|---|
| Autenticación de clientes y personal | Laravel Sanctum (tokens), dos guards separados (`clientes`, `personal`) |
| Autorización por rol | Policies y Gates nativos |
| Trabajos asíncronos | Queues con Redis + Laravel Horizon para monitoreo |
| Tareas programadas (expirar reservas, liberar cupos, anonimizar datos) | Scheduler nativo |
| Tiempo real | Laravel Reverb (ADR-007) |
| Validación y respuestas | Form Requests + API Resources; formato de error único (RFC 9457 *problem details*) |
| Contrato de API | OpenAPI 3.1 generado desde anotaciones y publicado en cada build (ADR-006) |
| Pruebas | PHPUnit/Pest, base de datos MySQL real en CI (no SQLite) |

MVC se aplica **dentro de cada módulo**: Controller (HTTP) → Servicio de aplicación / Acción → Modelo Eloquent. Los controladores no contienen reglas de negocio (ADR-005).

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| Symfony | Más flexible pero con más decisiones por tomar; Laravel trae colas, scheduler, WebSockets y autenticación listos. |
| Node.js (NestJS) | Permitiría un solo lenguaje con el frontend, pero la restricción del dueño y el conocimiento del equipo son Laravel. |
| Laravel con Blade/Livewire para el panel | Mezclaría dos frontends; con Angular ya elegido, un solo enfoque simplifica. |
| Autenticación con JWT propio | Sanctum resuelve tokens y revocación sin código propio; menos superficie de error de seguridad (ASVS). |

## Consecuencias
**Positivas:** ecosistema maduro y documentado, la mayor parte de la infraestructura (colas, tiempo real, autenticación) es de primera parte y se mantiene junta; MySQL es el camino más probado de Laravel (ADR-003).

**Negativas / riesgos:** PHP es síncrono por petición → el tiempo real y las tareas largas van siempre a colas y Reverb, nunca en la petición HTTP; Eloquent facilita romper fronteras entre módulos (una relación a un modelo de otro módulo) → se prohíbe por herramienta (ADR-005).

**Revisar si:** un módulo requiere latencia o concurrencia que PHP no dé (por ejemplo, optimización de rutas pesada); entonces ese módulo se extrae como servicio, lo que el diseño modular permite.
