# Módulos (ADR-005)

Cada carpeta es un bounded context del documento 02. Estructura obligatoria:

```
Modulo/
  Domain/          PHP puro: entidades, value objects, reglas, excepciones de dominio. Sin Laravel.
  Application/     Casos de uso (una clase por acción), DTOs, interfaces de puertos (repositorios).
  Infrastructure/  Eloquent, repositorios, adaptadores a otros módulos (vía sus Contracts).
  Http/            Controllers, FormRequests, Resources, routes.php  <- el MVC vive aquí.
  Contracts/       ÚNICA API pública del módulo: interfaces + DTOs. Lo único que otros módulos importan.
  Events/          Eventos publicados (clases con versión). Otros módulos pueden escucharlos.
  Database/        Migrations (prefijo del módulo), Seeders.
  Tests/           Unit (Domain), Feature (Http + Infra con MySQL real).
  <Modulo>ServiceProvider.php   registra rutas, bindings, listeners.
```

Reglas (deptrac las verifica en CI):
1. Solo se importa de otro módulo su `Contracts/` o `Events/`.
2. Sin relaciones Eloquent ni FK entre módulos; se guardan `uid` (04 §12).
3. `Domain/` no importa `Illuminate\*`.
4. Controllers: validar -> caso de uso -> Resource. Nada más.

Generar un módulo: `php artisan make:module Nombre` (comando propio en `app/Shared/Console`, se escribe en el paso 7).
