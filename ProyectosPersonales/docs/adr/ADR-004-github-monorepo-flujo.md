# ADR-004 · GitHub, monorepo y flujo de trabajo trunk-based

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Cristhian Rodriguez Ruiz (dueño), equipo de desarrollo |
| Relacionado con | 01 §7 (equipo de 2) · buenas prácticas DORA |

## Contexto
Equipo de dos personas (frontend y backend) que deben cambiar el contrato de API y ambos lados de forma coordinada. El dueño fijó GitHub. Se necesita un flujo que permita integrar a diario, revisar todo el código y desplegar sin ceremonia.

## Decisión
**Un solo repositorio en GitHub** con esta estructura:

```
dtoo-limpieza/
  docs/            → 01, 02, adr/, arquitectura, runbooks (docs as code)
  contracts/       → openapi.yaml (fuente de verdad de la API) y esquemas de eventos
  backend/         → Laravel
  frontend/        → workspace Angular (ADR-001)
  infra/           → docker-compose, GitHub Actions, scripts de despliegue
  README.md        → cómo levantar todo con un comando
```

**Flujo de trabajo**
- **Trunk-based:** rama `main` siempre desplegable y protegida. Ramas de trabajo cortas (≤ 2 días), nombradas `tipo/descripcion-corta` (`feat/reservas-inventario`).
- **Pull request obligatorio**, pequeño (objetivo ≤ 400 líneas), con revisión cruzada: el backend revisa al frontend y viceversa. Nadie mergea su propio PR sin aprobación.
- **Conventional Commits** (`feat:`, `fix:`, `docs:`, `refactor:`, `test:`) → changelog y versiones automáticas.
- **CI en GitHub Actions** en cada PR: lint + tipos → pruebas unitarias → integración (MySQL real en contenedor) → validación del contrato OpenAPI → build → análisis de dependencias y secretos. Un check rojo bloquea el merge.
- **Feature flags** para funcionalidad no terminada; no se usan ramas largas.
- **Despliegue:** merge a `main` despliega a *staging* automáticamente; a producción por promoción manual (un clic) con rollback.
- **Dependabot** para actualizaciones, mergeadas solo con CI verde.
- **Un PR que cambia `contracts/openapi.yaml` regenera el cliente TypeScript** y falla si el frontend deja de compilar (ADR-006).

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| Dos repositorios (frontend / backend) | Cada cambio de contrato requiere dos PRs coordinados; la documentación queda dividida. Con dos personas, la fricción supera el beneficio. |
| GitFlow (develop, release, hotfix) | Ramas largas y merges dolorosos; contrario a la evidencia DORA sobre lotes pequeños. |
| GitLab / Bitbucket | Equivalentes; restricción del dueño. |
| Herramienta de monorepo (Nx, Turborepo) desde el inicio | Útil cuando hay muchos paquetes; con dos aplicaciones, GitHub Actions con filtros de ruta basta. Se puede añadir después. |

## Consecuencias
**Positivas:** una sola historia de cambios para contrato, código y documentación; cada PR es revisado por la otra persona, lo que reparte el conocimiento; el pipeline hace de tercer revisor.

**Negativas / riesgos:** el CI corre para todo el repo → se usan filtros de ruta para ejecutar solo lo afectado; con dos personas, una ausencia bloquea revisiones → regla de excepción: el dueño puede aprobar PRs de documentación y configuración.

**Revisar si:** el equipo crece más allá de 5 personas o aparecen más de 5 paquetes; entonces evaluar Nx y CODEOWNERS por módulo.
