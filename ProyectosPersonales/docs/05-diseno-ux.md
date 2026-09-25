# 05 · Diseño UX: tienda, panel de operación y app del motorizado

| Campo | Valor |
|---|---|
| Proyecto | D'Too Limpieza |
| Estado | **v1.0 — CERRADO. Aprobado por el dueño del producto el 2026-09-14** |
| Fecha | 2026-09-14 |
| Depende de | 01 §6.6 (usabilidad) · 02 (estados y vocabulario) · 04 (datos que se capturan) · ADR-001, ADR-008 |
| Alimenta | Design system (`libs/design-system`), contrato OpenAPI, plan de desarrollo |

> Los esquemas de pantalla están en texto para poder revisarlos y versionarlos junto al código. El diseño visual final (colores, tipografía, marca) se define en el design system a partir de los tokens de §7; aquí se decide **qué hay en cada pantalla y en qué orden**, que es lo que determina la conversión y los errores operativos.

---

## 1. Principios de diseño

1. **El teléfono es la llave.** Todo lo del cliente gira alrededor de su número: reconocerlo, consultar su pedido, llamarlo. Sin correo obligatorio, sin cuenta obligatoria.
2. **El total en puerta es sagrado.** El cliente ve exactamente cuánto pagará antes de confirmar, sin sorpresas. Como no hay costo de envío ni mínimo, el total es la suma de productos, y así se dice.
3. **Menos pantallas, no menos claridad.** Checkout en 3 pasos para invitados; cada paso hace una sola cosa.
4. **Mobile-first en la tienda y la app; desktop-first en el panel.** Los clientes compran desde el celular; el administrador y almacén trabajan en pantalla grande, aunque el panel también funciona en tablet.
5. **Diseñado para la calle.** La app del motorizado se usa con una mano, al sol, con guantes y sin señal: botones grandes, alto contraste, confirmaciones claras, nunca un formulario largo.
6. **El sistema avisa, no bloquea** (02 §9). Los avisos se ven, se pueden posponer y se resuelven después.
7. **Accesible por defecto:** WCAG 2.2 AA en tienda y checkout; objetivos táctiles ≥ 44 px en la app del motorizado.
8. **Lenguaje de Perú, sin tecnicismos.** "Pedido", "entrega", "pago en puerta". Nunca "fulfillment", "checkout" ni "SKU" en pantallas de cliente.

## 2. Taxonomía del catálogo (propuesta para revisar)

Tres niveles máximo (04 §1). Los nombres deben coincidir con cómo el cliente busca, no con cómo el proveedor clasifica. Marcado con ✱ lo que suele tener presentación "para negocio" (galón, bidón, pack).

| Nivel 1 | Nivel 2 | Ejemplos de productos |
|---|---|---|
| **Lavandería** | Detergentes ✱ · Suavizantes ✱ · Quitamanchas y blanqueadores ✱ · Jabón en barra | Detergente líquido/polvo, lejía para ropa |
| **Limpieza del hogar** | Pisos ✱ · Multiusos ✱ · Vidrios · Muebles y madera · Antigrasa/cocina ✱ | Limpiador de pisos, desengrasante |
| **Baño** | Limpiadores de baño ✱ · Desinfectantes de inodoro · Antisarro/antihongos · Destapacaños | Pastillas para tanque, gel WC |
| **Desinfección** | Lejía ✱ · Alcohol ✱ · Desinfectantes en aerosol · Amonio cuaternario ✱ | Alcohol 70 %, lejía 5 % |
| **Lavavajillas** | Lavavajillas líquido ✱ · En pasta/crema ✱ · Esponjas y fibras | |
| **Aseo personal** | Jabón líquido de manos ✱ · Jabón en barra · Papel higiénico ✱ · Papel toalla ✱ · Servilletas ✱ | |
| **Ambientadores** | Aerosoles · Líquidos para pisos ✱ · Gel y aromatizantes · Insecticidas | |
| **Utensilios y accesorios** | Escobas y recogedores · Trapeadores y baldes · Paños y microfibra · Guantes ✱ · Bolsas de basura ✱ · Dispensadores | |
| **Para negocios** | *(categoría virtual)* | Muestra las presentaciones marcadas como formato negocio de todas las categorías; útil como acceso directo para bodegas y restaurantes |

**Atributos filtrables** (facetas): marca, presentación (tamaño), fragancia, uso (hogar / negocio), "en oferta". Decisión SEO (documento de buenas prácticas): se indexan categoría y subcategoría; las combinaciones de filtros llevan `noindex`.

Taxonomía **confirmada por el dueño** sin ajustes. Se carga como seeder inicial; las categorías se pueden editar desde el panel.

## 3. Tienda (cliente)

### 3.1 Mapa de navegación

```
Inicio
├── Categoría (/c/lavanderia) → Subcategoría (/c/lavanderia/detergentes)
│     └── Producto (/p/detergente-liquido-marca-x)
├── Buscar (campo siempre visible)
├── Para negocios (acceso directo)
├── Carrito (/carrito)
│     └── Checkout (/checkout) → Confirmación (/pedido/DT-000123)
├── Mi pedido (/mi-pedido) → consulta por número + teléfono
├── Mi cuenta (opcional) → pedidos anteriores, direcciones, "volver a pedir"
└── Pie: cómo funciona el pago en puerta · zona de cobertura · privacidad · contacto
```

### 3.2 Inicio (móvil)

```
┌──────────────────────────────────┐
│ D'Too Limpieza      [☰] [🛒 2]   │
│ [🔍 ¿Qué necesitas hoy?        ] │
│                                  │
│ 📍 Entregamos hoy en Carmen de   │
│    la Legua-Reynoso · Pago en    │
│    puerta: efectivo, Yape, Plin  │
│    o tarjeta                     │
│                                  │
│ Categorías                       │
│ [Lavandería] [Hogar] [Baño]      │
│ [Desinfección] [Vajilla] [Aseo]  │
│ [Ambientadores] [Utensilios]     │
│ [🏪 Para negocios]               │
│                                  │
│ Los más pedidos                  │
│ ┌──────┐ ┌──────┐ ┌──────┐      │
│ │ img  │ │ img  │ │ img  │ →    │
│ │Nombre│ │Nombre│ │Nombre│      │
│ │1 L   │ │Galón │ │Pack×6│      │
│ │S/ 12 │ │S/ 38 │ │S/ 20 │      │
│ │[+ Agregar]     ...              │
│ └──────┘ └──────┘ └──────┘      │
│                                  │
│ ¿Cómo funciona?                  │
│ 1 Pides → 2 Te confirmamos →     │
│ 3 Llega hoy → 4 Pagas al recibir │
└──────────────────────────────────┘
```

Decisiones: la franja de cobertura y pago va arriba porque responde las dos dudas que frenan al cliente nuevo ("¿llegan a mi zona?", "¿cómo pago?"). "Agregar" directo desde la tarjeta con selector de presentación si hay más de una.

### 3.3 Categoría / listado

- Encabezado con nombre, breve descripción (SEO) y chips de subcategorías.
- Filtros en panel deslizable (móvil) o columna (desktop): marca, presentación, uso, fragancia. Orden: más pedidos, precio menor a mayor, precio mayor a menor.
- Tarjeta de producto: imagen, nombre, presentación seleccionable, precio, **badge de disponibilidad en tiempo real** ("Disponible" / "Últimas unidades" / "Agotado"), botón agregar con control de cantidad.
- Productos agotados se muestran al final, atenuados, con **"Avísame cuando haya stock"** (confirmado): guarda teléfono y correo opcional; cuando Inventario emite `StockReingresado`, Notificaciones envía correo si lo hay y crea un aviso en el panel con la lista de interesados para llamar. Se añade la tabla `not_avisos_stock` (sku, telefono, correo, creado_en, notificado_en) al modelo de datos.
- Paginación por "Ver más" con URL de página real (`?pagina=2`) para SEO.

### 3.4 Producto

```
┌──────────────────────────────────┐
│ ← Lavandería › Detergentes       │
│ [   imagen principal    ] ○●○    │
│ Detergente líquido Marca X       │
│ Marca X · Floral                 │
│                                  │
│ Presentación                     │
│ (●) 1 L      S/ 12,90  Disponible│
│ ( ) Galón 4 L S/ 44,90 Últimas 3 │
│ ( ) Pack ×6   S/ 69,90 Agotado   │
│                                  │
│ [ − ] 1 [ + ]                    │
│ [   Agregar al carrito · S/ 12,90 ]│
│                                  │
│ ✓ Entrega hoy si pides antes de  │
│   las 18:00 · Pago al recibir    │
│                                  │
│ Descripción ▾                    │
│ Modo de uso ▾                    │
│ Precauciones ▾                   │
│                                  │
│ También te puede servir          │
│ [Suavizante] [Quitamanchas] …    │
└──────────────────────────────────┘
```

La promesa de entrega se calcula con el cupo real (04 §3): si el día está lleno dice "Entrega mañana desde las 7:00".

### 3.5 Carrito

- Lista de líneas con presentación, cantidad editable, precio, subtotal; eliminar con deshacer.
- **Total a pagar en puerta** destacado. Texto: "Sin costo de envío. Sin monto mínimo."
- Aviso si algún producto se agotó mientras estaba en el carrito (tiempo real): "Se agotó X; lo quitamos del carrito" con opción de cambiar presentación.
- Sugerencias "Completa tu compra" (máximo 3, misma categoría).
- Botón fijo abajo: **"Pedir · S/ 86,00"**. Enlace secundario "Seguir comprando".

### 3.6 Checkout (3 pasos para invitados)

Barra de progreso: **1 Tus datos → 2 Entrega → 3 Pago y confirmar**. Los datos se guardan al pasar de paso; volver atrás no borra nada. El total siempre visible en una franja fija.

**Paso 1 · Tus datos**
```
Teléfono (obligatorio)  [ +51 9__ ___ ___ ]
  ↳ Si el teléfono ya existe: "¡Hola de nuevo, Rosa! Usamos tus datos guardados" y salta a Entrega con la dirección predeterminada.
Nombre y apellido       [                 ]
Correo (opcional)       [                 ]  "Para enviarte el resumen. Si no tienes, no pasa nada."
¿Para tu hogar o para tu negocio?  (●) Hogar  ( ) Negocio
  ↳ Negocio: campo "Nombre del negocio (opcional)" y precios cambian a lista NEGOCIO con aviso.
[ Continuar ]
```

**Paso 2 · Entrega**
```
Dirección   [ Av. ... 123                 ]  (autocompletado del proveedor de mapas)
[ mapa con pin arrastrable ]  "Mueve el pin hasta tu puerta"
Referencia (obligatoria) [ Frente al parque, reja negra ]
  ↳ Si el pin cae fuera de la zona: "Por ahora solo entregamos en Carmen de la Legua-Reynoso. Déjanos tu teléfono y te avisamos cuando lleguemos a tu zona." (guarda lead)
¿Cuándo te lo llevamos?
  (●) Hoy · 15:00–18:00   (3 cupos)
  ( ) Hoy · 18:00–21:00
  ( ) Mañana · 7:00–10:00
  (franjas llenas aparecen atenuadas: "Completo")
[ Continuar ]
```

**Paso 3 · Pago y confirmar**
```
¿Cómo vas a pagar al recibir?
  (●) Efectivo    ( ) Yape    ( ) Plin    ( ) Tarjeta (débito o crédito)
  ↳ Efectivo: "¿Con cuánto pagarás?" [ S/ 100 ]  → "Tu vuelto: S/ 14,00"
     Atajos: [Exacto] [S/ 50] [S/ 100] [S/ 200]. Validación: no menor al total.
  ↳ Tarjeta: "El motorizado lleva POS; puedes pagar con débito o crédito."
Nota para el motorizado (opcional) [ Tocar el timbre 2 veces ]

Resumen
  3 productos ······················ S/ 86,00
  Envío ······························ Gratis
  ─────────────────────────────────────
  TOTAL A PAGAR EN PUERTA ·········· S/ 86,00
  Entrega: hoy 15:00–18:00 · Av. ... 123

☐ Quiero recibir ofertas (opcional)          ← consentimiento marketing, separado
Al pedir aceptas los Términos y la Política de privacidad (enlaces)

[ Confirmar pedido ]
```

Al confirmar: se muestra una pantalla de carga breve mientras el servidor reserva stock y cupo. Errores posibles y su tratamiento:
- Stock insuficiente → vuelve al carrito con las líneas afectadas marcadas y alternativas.
- Cupo agotado → vuelve al paso 2 con la franja marcada "Se acaba de llenar" y la siguiente preseleccionada.
- Red caída → "No pudimos confirmar. Revisa tu conexión e intenta de nuevo"; el reintento usa la misma clave, así que nunca se duplica el pedido.

**Paso 3 para cliente registrado con historial:** misma pantalla, con dirección y método habituales preseleccionados; puede confirmar en dos toques.

### 3.7 Confirmación del pedido

```
✓ ¡Pedido recibido!  N.º DT-000123
Estado: Confirmado ✓  (o "Lo estamos revisando; te llamaremos al 9__ ___ ___ en breve")
Entrega: hoy 15:00–18:00
Pagas al recibir: S/ 86,00 en efectivo (llevamos vuelto de S/ 14,00)

Guarda tu número de pedido. Puedes ver el estado en cualquier momento en
"Mi pedido" con tu teléfono.
[ Ver mi pedido ]   [ Seguir comprando ]
¿Quieres crear una cuenta para pedir más rápido la próxima? Solo una contraseña: [ ____ ] [Crear]
```

Si el pedido quedó en revisión, el mensaje es honesto y da el teléfono al que llamaremos; es la forma de que el cliente esté atento.

### 3.8 Mi pedido (seguimiento sin cuenta)

Entrada: número de pedido + teléfono. Muestra una línea de tiempo con los estados en lenguaje de cliente:

| Estado interno | Texto para el cliente |
|---|---|
| Creado / PendienteDeRevision | Recibido · Lo estamos revisando |
| Confirmado / EnPreparacion / ListoParaDespacho | Confirmado · Preparando tu pedido |
| EnRuta | En camino · Llega entre 15:00 y 18:00 |
| Entregado / Cobrado | Entregado ✓ |
| EntregaFallida | No pudimos entregarlo · Te contactaremos para reprogramar |
| Cancelado | Cancelado |
| Devuelto | Devuelto al almacén |

Acciones: cancelar (solo antes de `EnPreparacion`), "Volver a pedir lo mismo", llamar a la tienda.

### 3.9 Cuenta (opcional)

Pedidos anteriores con "Volver a pedir", direcciones guardadas, datos, preferencias de contacto, botón "Descargar mis datos" y "Eliminar mi cuenta" (ARCO).

## 4. Panel de operación (administrador y almacén)

### 4.1 Estructura

Barra lateral con módulos; arriba, buscador global (número de pedido, teléfono, nombre) y campana de avisos.

| Sección | Quién | Qué hace |
|---|---|---|
| **Hoy** (inicio) | Admin, almacén | Tablero del día: pedidos por estado, cupo usado por franja, motorizados activos y sus rutas, avisos pendientes |
| **Pedidos** | Admin | Lista con filtros por estado/fecha/franja; ficha con historial, señales de riesgo y botones **Confirmar / Rechazar / Cancelar**; crear pedido manual (canal `panel`, para pedidos por teléfono) |
| **Preparación** | Almacén | Cola de pedidos confirmados por franja; lista de recolección; marcar preparado; reportar faltante; imprimir nota de pedido |
| **Rutas** | Admin | Armar rutas por franja y motorizado; ordenar paradas (mapa + lista); ver vuelto total a llevar; iniciar/cerrar; reasignar |
| **Caja** | Admin | Cierres del día por motorizado: esperado vs. declarado por método; justificar diferencias; historial |
| **Catálogo** | Admin | Productos, presentaciones, precios (con historial), imágenes, categorías |
| **Inventario** | Admin, almacén | Existencias, entradas, ajustes con motivo, reservas activas, recepción de devoluciones |
| **Clientes** | Admin | Ficha por teléfono: pedidos, rechazos, direcciones; lista de observación |
| **Configuración** | Admin | Zona y franjas, cupos del día, reglas de riesgo y umbral, interruptor "confirmación manual para todos", plantillas de correo, usuarios y roles |

### 4.2 Pantalla "Hoy" (desktop)

```
┌────────────────────────────────────────────────────────────────────────┐
│ Hoy · Lun 14 set                                   🔔 3   [Buscar ⌕]  │
├──────────────────┬───────────────────────┬─────────────────────────────┤
│ Pedidos del día  │ Cupo por franja       │ Motorizados                 │
│ En revisión   4 ●│ 7–10   ████████░░ 8/10│ Juan  · Ruta 2 · 5/7 ✓      │
│ Confirmados   9  │ 10–13  ██████░░░░ 6/10│ Pedro · Ruta 1 · cerrada    │
│ En preparación 3 │ 13–16  ███░░░░░░░ 3/10│ Luis  · sin ruta            │
│ Listos        6  │ 16–19  █░░░░░░░░░ 1/10│ [Armar ruta 16–19]          │
│ En ruta       7  │ 19–21  ░░░░░░░░░░ 0/6 │                             │
│ Entregados   22  │ [Ajustar cupos de hoy]│                             │
│ Fallidos      1 ●│                       │                             │
├──────────────────┴───────────────────────┴─────────────────────────────┤
│ Avisos                                                                 │
│ ● Pedido DT-000131 en revisión vence en 25 min          [Ver]         │
│ ● Cierre de caja de Pedro (ayer) sigue abierto           [Ver]         │
│ ● Lejía 1 L: agotada                                     [Ver]         │
└────────────────────────────────────────────────────────────────────────┘
```

Todo se actualiza en tiempo real (ADR-007). Los números son enlaces a la lista filtrada.

### 4.3 Ficha de pedido en revisión

Cabecera: número, cliente (nombre, **teléfono con botón "Llamar"**, segmento, "3 pedidos previos / 1 rechazo"), total, franja, método de pago y vuelto. Bloque "Por qué está en revisión": lista de señales con su peso y el puntaje. Historial de estados. Botones grandes: **Confirmar pedido** · **Rechazar** (pide motivo) · **Editar dirección/franja** (con el cliente al teléfono). Tiempo restante de revisión visible.

### 4.4 Preparación (almacén, tablet o PC)

- Columna de pedidos confirmados agrupados por franja; el más urgente arriba.
- Al abrir: lista de recolección ordenada por categoría/ubicación en almacén, con casillas grandes; cantidad y presentación en letra grande.
- Botón **Imprimir nota de pedido** (confirmado: un papel simple con el detalle de la compra, **no es comprobante fiscal**). Formato ticket o A5: nombre de la tienda, número de pedido, fecha, cliente y teléfono, dirección y referencia, líneas con cantidad y precio, total a pagar, método de pago declarado y vuelto si aplica, nota del cliente. Sin logo obligatorio; se puede añadir después desde plantillas.
- **Reportar faltante** por línea → el pedido pasa a `con_faltante` y avisa al administrador.
- **Listo para despacho** → el pedido aparece en Rutas.

### 4.5 Rutas

- Vista por franja: lista de pedidos listos (con dirección, peso estimado, método de pago) y mapa con pines.
- Crear ruta: elegir motorizado y salida; arrastrar pedidos o "Agregar seleccionados"; sugerencia de orden por cercanía (cálculo simple del vecino más cercano en fase 1).
- Resumen de la ruta: n.º de paradas, peso total, **efectivo esperado y vuelto que debe llevar**, aviso si supera 30 pedidos (solo aviso).
- **Iniciar ruta** → la app del motorizado la recibe. Reasignar parada a otra ruta en cualquier momento.

### 4.6 Caja

Tabla por motorizado y día: esperado por método (efectivo / Yape / Plin / tarjeta), declarado, diferencia. Fila con diferencia en rojo; al abrirla, lista de cobros de ese día y campo de justificación obligatorio; botón "Registrar ajuste y cerrar". Cierres abiertos de días anteriores arriba con aviso permanente.

## 5. App del motorizado (PWA, celular)

### 5.1 Principios específicos
- Una pantalla = una decisión. Botones de ≥ 56 px de alto.
- Modo alto contraste por defecto (sol); tema oscuro opcional para la noche.
- Indicador permanente de conexión y de operaciones pendientes de sincronizar.
- Todo lo que el motorizado necesita para una parada está en la parada: no hay que navegar.

### 5.2 Ruta del día

```
┌──────────────────────────────┐
│ Ruta 2 · 16:00–19:00   ● 4G  │
│ 5 de 7 entregadas            │
│ Efectivo a llevar: S/ 60     │
│ ───────────────────────────  │
│ ▶ 6  Av. Principal 456       │
│      Rosa M. · S/ 86 efectivo│
│      Vuelto: S/ 14           │
│      [ 📞 ]  [ 🗺 Ir ]  [Abrir]│
│ ───────────────────────────  │
│   7  Jr. Los Pinos 12        │
│      Bodega El Sol · Yape    │
│      S/ 210                  │
│ ───────────────────────────  │
│ ✓ 1–5 entregadas ▾           │
│                              │
│ [ Cerrar ruta ]              │
└──────────────────────────────┘
```

"Ir" abre la app de mapas del celular con las coordenadas. "Llamar" marca directo.

### 5.3 Parada

```
┌──────────────────────────────┐
│ ← Parada 6 de 7              │
│ Rosa Mendoza  [ 📞 Llamar ]  │
│ Av. Principal 456            │
│ Ref: frente al parque, reja  │
│      negra                   │
│ Nota: tocar timbre 2 veces   │
│                              │
│ Entregar: 3 productos ▾      │
│ COBRAR: S/ 86,00             │
│ Paga con S/ 100 → vuelto S/ 14│
│                              │
│ [    ✓ ENTREGADO Y COBRADO   ]│
│                              │
│ [ ✗ No se pudo entregar ]    │
└──────────────────────────────┘
```

**Entregado y cobrado** → pantalla de cobro:
```
¿Cómo pagó?  [Efectivo] [Yape] [Plin] [Tarjeta]   (preseleccionado lo declarado)
Monto a cobrar   S/ 86,00   (fijo: siempre el total del pedido)
Si paga en efectivo: recibe S/ 100 → entrega vuelto S/ 14,00
N.º de operación (Yape/Plin/tarjeta, opcional) [        ]
[ 📷 Foto (opcional) ]
[ CONFIRMAR ]
```
Confirmación con vibración y pantalla verde 1 s; vuelve a la ruta con la siguiente parada arriba.

**Regla confirmada por el dueño:** el motorizado **siempre cobra el total completo**; no existen pagos parciales ni fiados. Si el cliente pagó con más efectivo, el motorizado entrega el vuelto exacto que la app le muestra. Si el cliente no puede pagar el total, la entrega no se realiza y se registra como "No se pudo entregar · Cliente no puede pagar". En el modelo de datos, `ful_paradas.monto_recibido` y `cob_cobros.monto_recibido` quedan siempre iguales al total; se conservan solo para auditoría y para el futuro.

**No se pudo entregar** → motivos en botones: "Cliente no está" · "Cliente rechazó" · "Cliente no puede pagar" · "Dirección no existe" · "Otro" (+ nota). Botón "Llamar" antes de confirmar. Registra y avisa al administrador.

### 5.4 Sincronización y cierre

- Barra inferior: "● Todo sincronizado" / "⟳ 2 pendientes" / "⚠ Sin conexión · se enviará al reconectar".
- Al **Cerrar ruta**: resumen (entregadas, fallidas, efectivo/Yape/Plin/tarjeta recaudado) y estado de sincronización. Si hay pendientes, muestra "Se cerrará cuando se sincronice" y **no bloquea**.
- Al final del día, **Declarar caja**: campos por método con lo que entrega; el sistema compara y muestra la diferencia antes de enviar.

## 6. Estados de pantalla, errores y microcopy

Cada lista o formulario define sus cuatro estados: **cargando** (esqueletos, no spinners), **vacío** (mensaje + acción: "Aún no hay pedidos para esta franja"), **error** (qué pasó y qué hacer: "No pudimos guardar. Reintentar") y **con datos**.

Reglas de texto:
- Voz de "tú", frases cortas, sin mayúsculas gritonas.
- Números de dinero siempre con "S/ " y dos decimales.
- Nunca culpar al usuario: "El pin cayó fuera de nuestra zona" en lugar de "Dirección inválida".
- Los errores del servidor (stock, cupo, transición) tienen código estable (03 §8.3) y aquí se traduce cada uno a una frase.

## 7. Design system (base)

**Tokens** (marca confirmada: **azul y amarillo**, con fondos claros y cálidos para que la tienda no abrume):
- Primario azul: `#1E5EFF` (botones principales, enlaces, encabezados); azul oscuro `#0F2D6B` para texto de títulos.
- Acento amarillo: `#FFC53D` (botón "Pedir", destacados, badge "Últimas unidades"); sobre amarillo el texto va en azul oscuro para cumplir contraste.
- Fondos cálidos: crema `#FFF9EE` (fondo general de la tienda), blanco `#FFFFFF` (tarjetas), arena `#F4EBDD` (secciones alternas). Texto principal gris cálido `#2B2A28`, secundario `#6B665E`.
- Semánticos: éxito `#1B8A4C`, alerta `#C77700`, error `#C0392B`, información = primario.
- Panel y app del motorizado usan la misma paleta pero con más blanco y menos crema, para legibilidad en pantallas de trabajo. Contraste ≥ 4,5:1 verificado en cada par texto/fondo.
- Tipografía: una familia sans (sistema o Inter), escala 14/16/18/24/32; en la app del motorizado el cuerpo es 18 y los importes 28.
- Espaciado en múltiplos de 4; radios 8; sombras mínimas.
- Objetivos táctiles: 44 px tienda, 56 px app motorizado.

**Componentes compartidos** (Angular Material como base, envueltos en `libs/design-system`): botón (primario/secundario/peligro, con estado cargando), campo de texto con validación en línea, selector de cantidad, tarjeta de producto, badge de disponibilidad, chip de estado de pedido (mapa de colores por estado), línea de tiempo de estados, tabla con filtros (panel), tarjeta de parada (motorizado), banner de conexión, diálogo de confirmación, toast con deshacer.

**Accesibilidad:** foco visible, etiquetas en todos los campos, mensajes de error asociados por `aria-describedby`, navegación por teclado completa en panel y checkout, texto alternativo obligatorio en imágenes de catálogo (04 `cat_imagenes.alt`), sin depender solo del color.

## 8. Métricas UX a instrumentar desde el día 1

| Métrica | Dónde | Meta (01 §5) |
|---|---|---|
| Conversión checkout (iniciado → confirmado) | Tienda | ≥ 45 % |
| Abandono por paso del checkout | Tienda | Detectar el paso que más pierde |
| Tiempo medio del checkout invitado | Tienda | ≤ 3 min |
| Pedidos con pin fuera de zona | Tienda | Leads para fase 2 |
| Tiempo confirmado → listo para despacho | Panel | ≤ 45 min |
| Tiempo por parada (abrir → confirmar) | App | ≤ 3 min |
| Errores de sincronización | App | 0 pérdidas |
| Diferencias de caja / cierres | Panel | 0 sin justificar |

## 9. Plan de validación

1. **Prueba con 5 clientes reales** (3 hogar, 2 negocio) del prototipo de tienda: tarea "compra detergente y lejía para hoy pagando en efectivo con S/ 100". Medir si entienden la franja, el "pago con" y la confirmación.
2. **Prueba con los 3 motorizados** de la app en la calle, con datos móviles apagados a mitad de ruta.
3. **Prueba con almacén** de la cola de preparación y la nota impresa.
4. Ajustar y congelar los flujos antes de que el frontend construya pantallas finales.

## 10. Decisiones confirmadas por el dueño

| Tema | Decisión |
|---|---|
| Taxonomía | Sin ajustes; se carga tal cual |
| "Avísame cuando haya stock" | Sí; se implementa con aviso al panel y correo opcional |
| Cobro en puerta | Siempre el total completo; sin parciales; vuelto exacto si paga con más efectivo |
| Nota de pedido | Papel simple con el detalle de la compra; no es comprobante fiscal; sin logo obligatorio |
| Paleta | Azul y amarillo como marca; fondos claros y cálidos |

## 11. Historial de versiones

| Versión | Fecha | Cambio |
|---|---|---|
| 0.1 | 2026-09-14 | Borrador inicial |
| 1.0 | 2026-09-14 | Decisiones del dueño aplicadas; cerrado |
