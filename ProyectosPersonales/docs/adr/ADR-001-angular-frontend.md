# ADR-001 · Angular como frontend para tienda, panel y app de motorizado

| Campo | Valor |
|---|---|
| Estado | Aceptado |
| Fecha | 2026-09-14 |
| Decisores | Cristhian Rodriguez Ruiz (dueño), desarrollador frontend |
| Relacionado con | 01 §6.2, §6.6, §7 · 02 §4.7, §4.10 |

## Contexto
Hay tres interfaces: la tienda pública (necesita SEO y rendimiento en móviles 4G), el panel de operación (uso intensivo por 2–3 personas) y la app del motorizado (celular, exterior, conexión intermitente). El equipo tiene **un** desarrollador frontend. El dueño fijó Angular como restricción.

## Decisión
Angular, en su versión estable vigente al crear el repositorio (se fija en `package.json` y se actualiza con cada versión mayor), para las tres interfaces, organizadas en **un solo workspace** con tres aplicaciones y librerías compartidas:

```
frontend/
  apps/tienda/        → SSR con Angular SSR (SEO, Core Web Vitals)
  apps/panel/         → SPA, PWA instalable
  apps/motorizado/    → PWA con soporte fuera de línea (ADR-008)
  libs/design-system/ → componentes, tokens, accesibilidad
  libs/api-client/    → cliente TypeScript generado desde OpenAPI (ADR-006)
  libs/dominio/       → tipos y reglas compartidas (estados de pedido, formatos de dinero)
```

**Costo:** la PWA es una capacidad estándar de la web (manifiesto + service worker que Angular genera con `ng add @angular/pwa`); no tiene costo ni requiere publicarse en tiendas de aplicaciones.

Prácticas obligatorias: componentes standalone, signals para estado local, `strict: true` en TypeScript, formularios tipados, lazy loading por ruta, Angular Material como base del design system (accesibilidad WCAG 2.2 AA incluida) con tokens propios.

## Alternativas consideradas
| Alternativa | Por qué se descartó |
|---|---|
| React o Vue | Equivalentes técnicamente; restricción del dueño y conocimiento del equipo favorecen Angular. Angular además trae router, formularios, HTTP, SSR y PWA integrados, lo que reduce decisiones para un equipo de uno. |
| Tres repositorios separados | Triplica la configuración y dificulta compartir el design system y el cliente de API. |
| App nativa para el motorizado | Segundo código base y publicación en tiendas; inasumible con un frontend. La PWA cubre GPS, cámara y fuera de línea (ADR-008). |
| Sin SSR en la tienda | Perjudica indexación y LCP; los requisitos de SEO y rendimiento del 01 lo exigen. |

## Consecuencias
**Positivas:** un solo lenguaje y herramienta para todo el frontend; el design system y los tipos del dominio se escriben una vez; SSR y PWA son capacidades nativas del framework.

**Negativas / riesgos:** Angular tiene curva de aprendizaje y versiones mayores cada seis meses → se reserva tiempo de actualización en cada ciclo; el SSR añade complejidad de despliegue → solo la tienda lo usa, panel y motorizado son estáticos.

**Revisar si:** el equipo frontend crece y aparecen necesidades nativas reales (notificaciones push complejas, Bluetooth de impresoras); entonces evaluar Capacitor sobre el mismo código Angular antes que una app nativa.
