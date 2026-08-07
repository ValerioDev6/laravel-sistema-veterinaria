# Prompts — Sistema Veterinaria

> Registro histórico de cada prompt ejecutado, con fecha.

---

## Prompt #1 — 2026-08-06

**Tipo:** Planificación inicial

**Prompt:**

Lee `proyecto-veterinaria.md` completo (fuente de verdad funcional) y el dump de base de datos ya provisto (20 tablas, modelos ya creados).

Vas a generar `plan-implementacion.md` con el desglose de TODAS las fases (1 a 8, la Fase 9 de reportes queda fuera por ahora) en tareas granulares. Para cada módulo de cada fase, la tarea se desglosa en el orden de construcción definido en la spec (API primero, Blade después):

1. Form Request
2. Action (+ transacción si escribe en 2+ tablas)
3. Filters, si el listado necesita filtros
4. Controller Api/Admin/* + ruta en routes/api.php
5. Resource
6. Controller Admin/* + ruta en routes/web.php
7. Vista Blade (reutilizando el HTML de Velzon ya existente) + JS en resources/js/pages/ consumiendo con Axios

No implementes nada todavía — solo genera el plan.

Además, crea:

- `tasks.md`: checklist con cada tarea del plan, estado "pendiente"
- `project-map.md`: documento inicial vacío con la estructura que vas a ir llenando fase por fase (módulos, rutas, Actions, Filters, Resources, vistas existentes)
- `prompts.md`: registra este mismo prompt como primera entrada, con fecha de hoy

Al terminar, muéstrame el plan completo para que lo revise antes de que empieces a construir.

**Resultado:** Se generaron los 4 archivos de documentación viva del proyecto.

---

## Prompt #2 — 2026-08-06

**Tipo:** Continuación de implementación

**Prompt:**

Lee `project-map.md` y `tasks.md` completos antes de tocar nada — son la fuente de verdad del estado real del sistema. Las Fases 1 a 6 ya están completadas y verificadas; no las repases, no las modifiques, no regeneres nada de lo que ya existe (modelos, migraciones, las 20 tablas, Actions, Resources, vistas, JS, seeders de esas fases).

Continúa exactamente donde quedó el trabajo: **Fase 7 — Facturación y pagos**, y después **Fase 8 — Recordatorios**. La Fase 9 (Reportes) sigue fuera de alcance.

Sigue el mismo orden de construcción usado en las fases anteriores, documentado en `project-map.md`:

1. Form Request (Store/Update)
2. Action (usar `DB::transaction()` si la operación escribe en 2 o más tablas — ej. generar un `invoice` desde una cita/vacuna/cirugía completada, o registrar un `payment` que además actualiza el estado/`remaining_balance` del `invoice`)
3. Filters (pipeline en `App\Filters\`) si el listado necesita filtros — mismo patrón que `Citas`
4. Controller `Api/Admin/*` + ruta en `routes/api.php` (prefijo de nombre `admin.api.*`)
5. Resource
6. Controller `Admin/*` + ruta en `routes/web.php`
7. Vista Blade — reutiliza el sidebar y layout de Velzon que ya existen, no crees ni modifiques `resources/views/layouts/app.blade.php` ni el menú del sidebar (ya está completo, solo activa los `href` de Facturación y Recordatorios que ya apuntan a `#`)
8. JS (misma configuración ya establecida en fases anteriores)
9. Seeder con datos realistas para el módulo

No toques el esquema de base de datos — las 20 tablas y sus modelos ya existen tal como están.

Al terminar Fase 7, **detente** y actualiza `project-map.md` y `tasks.md` con el estado real de lo construido (mismo formato de tablas usado en fases anteriores), antes de continuar con Fase 8. No apliques ni avances nada más allá de lo pedido hasta que confirme que revisé y está bien.

Al terminar Fase 8, detente igual, actualiza `project-map.md` / `tasks.md`, y espera confirmación antes de seguir. Registra este mismo prompt en `prompts.md` como Prompt #2, con la fecha de hoy.

**Resultado:** Se registró el prompt y se inició la Fase 7 — Facturación y pagos.

---

## Prompt #3 — 2026-08-06

**Tipo:** Fix de UI (sidebar)

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, vistas, módulos)

No implementes nada de fases pendientes. Este es un fix puntual de UI sobre lo que ya existe.

**Problema a corregir:**

Revisa `resources/views/layouts/app.blade.php` (sidebar de Velzon) y todas las vistas `admin/**/index.blade.php` ya construidas. Hay dos problemas de inconsistencia visual:

1. **Tamaño de fuente inconsistente**: los módulos simples estaban anidados en submenús colapsables (`nav-sm`), rompiendo la tipografía top-level de Velzon. Convierte un solo enlace por módulo apuntando a `index`.
2. **Menús redundantes**: el sidebar tenía subenlaces "Crear X"/"Nuevo X" que duplican el botón que ya existe en cada `index.blade.php`. Eliminar esos submenús redundantes; el botón de crear vive únicamente en la vista de listado.

**Alcance:** Solo este fix. No aplicares cambios de funcionalidad, no avances fases, no toques Actions/Controllers/Requests.

**Al terminar:** crea `fixes/2026-08-06-sidebar-ui-inconsistencia.md` documentando hallazgos y correcciones; actualiza la tabla "Fixes aplicados" en `project-map.md`; registra este prompt en `prompts.md` con la fecha de hoy. Detente al terminar.

**Resultado:** Se normalizó el sidebar (menús simples como `nav-link menu-link` top-level, tipografía Velzon uniforme, sin submenós de crear redundantes), se documentó el fix y se registró el prompt.

---

## Prompt #4 — 2026-08-06

**Tipo:** Fix de bug + fix de UI/routing

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, vistas, módulos)

No implementes nada de fases pendientes. Estos son dos fixes puntuales.

### Problema 1 — Error en `resources/views/admin/medical-records/index.blade.php:5`

La vista tira `ErrorException` en la línea 5. Investiga la causa real antes de parchar a ciegas:

- Revisa `app/Http/Controllers/Admin/MedicalRecordController@index` — confirma qué variable(s) está pasando a la vista y compáralas con lo que la vista espera en la línea 5 y alrededores
- Causas típicas a descartar: variable no definida/no pasada desde el controller, relación no cargada con `with()` que la vista intenta acceder (`->paciente->name`, etc.), o un `foreach`/acceso a colección que llega `null` cuando no hay registros (falta el empty state)
- Corrige la causa raíz, no solo silencies el error con `??` sin entender por qué falta el dato
- Verifica que el empty state siga funcionando cuando no hay historiales médicos

### Problema 2 — `/admin/invoices` vs `/admin/invoices#pagos`

Aclara y deja consistente el flujo de Facturación y Pagos. Ahora mismo hay dos entradas de menú/enlaces que aparentan ser rutas distintas, pero `#pagos` es un fragmento de URL (ancla), **no genera una ruta ni un controlador distinto** — si ambos enlaces apuntan al mismo controller/vista, están cargando exactamente el mismo contenido completo sin diferenciar nada, lo cual confunde al usuario (parece un link roto o un submenú que no lleva a ningún lado real).

Decide y deja implementado **uno de estos dos patrones**, el que sea consistente con cómo ya armaste Pacientes (vista única con tabs vía AJAX):

**Opción recomendada — una sola vista `admin/invoices/index.blade.php` con tabs Bootstrap:**

- Un tab "Facturas" (listado de `invoices`) y un tab "Pagos" (listado de `payments`), usando el componente de tabs nativo de Velzon
- Al cargar la página, si la URL trae `#pagos`, el JS debe activar ese tab automáticamente (leer `location.hash` en el `pages/invoices.js` y disparar `data-bs-toggle="tab"` correspondiente)
- Una sola ruta `admin.invoices.index`, un solo entry en el sidebar
- Elimina cualquier segundo entry de sidebar que apunte a `/admin/invoices#pagos` como si fuera una página aparte — el hash se maneja solo con JS, no con un segundo `<a href>` de sidebar

Si por el contrario ya decidiste que Pagos merece su propia página real (`/admin/invoices/payments` con su propio controller/vista), entonces:

- Crea la ruta real `admin.invoices.payments` (no un ancla)
- Actualiza el sidebar para que apunte a esa ruta real, no a `#pagos`

Elige la opción que menos código duplicado genere dado lo que ya existe de Fase 7 (revisa `project-map.md` para ver qué tanto de Invoices/Payments ya está construido antes de decidir).

**Alcance:** Solo estos dos fixes. No avances fases, no toques módulos no relacionados.

**Al terminar:** crea `fixes/2026-08-06-medical-records-error-e-invoices-rutas.md` documentando la causa raíz del error en medical-records, qué opción se eligió para Invoices/Pagos y por qué; actualiza la tabla "Fixes aplicados" en `project-map.md`; registra este prompt en `prompts.md` con la fecha de hoy. No hagas nada más allá de estos dos fixes. Detente al terminar y espera confirmación.

**Resultado:** Se corrigió la causa raíz del `ErrorException` (`$title` no pasada desde `MedicalRecordController@index`/`create`/`show`) y se consolidó el flujo Facturas/Pagos en una sola vista con tabs Velzon (nuevo `admin.api.payments.index`, tab de Pagos, activación por `location.hash`, single entry "Facturas" en el sidebar).

---

## Prompt #5 — 2026-08-06

**Tipo:** Mejora (pipeline de filtros + paginación server-side) + auditoría de reglas de negocio

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, vistas, módulos)

No toques auth (Breeze/Spatie) ni `routes/web.php`. Alcance estricto: capa Api/Admin + JS de páginas de listado.

### Parte 1 — Pipeline de filtros incompleto

Solo Citas y Facturas usan el pipeline `App\Filters\Pipeline::apply`. Identifica los listados cuyos filtros reales (los que se usan en el frontend) no están aplicados y crea/usa filters `handle(Builder, Request): Builder` para los que tengan uso real. No fuerces filters en listados sin filtro frontend real.

### Parte 2 — Paginación server-side

Los `index` de los `Api/Admin/*Controller` devuelven todo de golpe y el frontend usa DataTable client-side. Convierte a DataTable **server-side** real de forma consistente, definiendo un contrato propio `{ data, meta }` (lee `start`/`length` en el backend, no `page`). Elementos por listado: 15. Mantén transformaciones de presentación (renderAcciones, badges, montos `$`/`S/`, labels) en funciones separadas reutilizadas por `dataSrc`.

### Parte 3 — Reglas de negocio incompletas en Actions

`CreateCitaAction` ya valida que el veterinario no tenga otra cita en conflicto de horario (cruce contra `veterinarian_schedules` + otras citas del mismo vet/fecha/hora). Audita si existen reglas de negocio equivalentes **faltantes** en otros módulos con el mismo tipo de conflicto potencial (agenda/horario/recurso compartido):

- Confirma que la regla de Citas realmente existe y vive donde se dice (Action vs Form Request) y documenta el hallazgo real (no supongas).
- Audita Cirugías (`surgery_date`) y Vacunas (`vaccination_date`) — ¿tienen ese tipo de conflicto? Si no (solo fecha, sin hora, atados a `cita_id`), dilo explícito.
- Confirma que `DeleteOwnerAction` ya valida propietarios con mascotas.

**No agregues reglas de negocio inventadas** sin sentido clínico; si un módulo no necesita validación cruzada, dilo explícitamente en el documento en vez de forzar código.

**Tabla final en el doc:** módulo → regla evaluada → resultado (se agregó / ya existía / no aplica y por qué).

**Al terminar:** crea `fixes/2026-08-06-pipeline-filtros-y-paginacion-server-side.md` documentando hallazgos, decisión del contrato `{data, meta}` y lo de la auditoría de reglas; actualiza la tabla "Fixes aplicados" y "Filters registrados" (y Decisiones técnicas si procede) en `project-map.md`; registra este prompt en `prompts.md` con la fecha de hoy. No hagas nada más allá de este fix. Detente al terminar y espera confirmación.

**Resultado:** Se extendió el pipeline a Breeds (`FiltrarPorSpecies`) y MedicalRecords (`FiltrarPorPet`); se creó el helper `App\Filters\DataTables::paginate` (contrato propio `{data, meta}`, `start`/`length`, default 15) con los que los 16 `Api/Admin/*Controller` responden paginado y los 16 JS se convirtieron a DataTable server-side. Auditoría de reglas: la regla de conflicto de Citas está en los Form Requests `after()` (no en la Action como asumía el prompt); Cirugías/Vacunas no requieren bloqueo cruzado (solo fecha, atados a `cita_id`); `DeleteOwnerAction` ya valida mascotas. No se agregaron reglas inventadas.

---

## Prompt #6 — 2026-08-06

**Tipo:** Corrección de arquitectura (migración a DataTables server-side real, contrato oficial)

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido

**Contexto del bug:** la paginación del backend (`->paginate(15)` en los `index()` de Api/Admin) rompió los listados de DataTables. Causa raíz: las tablas usan el contrato propio `{data, meta}` y el `ajax.url` apunta a la ruta web `/admin/{modulo}` (HTML), no a la API `/api/admin/{modulo}` (JSON); además quedan llamadas crud con el prefijo inválido `/admin-api/...`.

**Decisión de fix:** migrar a **DataTables server-side real** con el **contrato oficial `{draw, recordsTotal, recordsFiltered, data}`**, y corregir las URLs a la API.

### Alcance (toca ambas capas de datos)

- `app/Http/Controllers/Api/Admin/**` — todos los `index()` que alimentan una tabla.
- `app/Filters/DataTables.php` — helper `server()` (contrato oficial con `start/length/search[value]/order`).
- `public/js/pages/*.js` — `ajax.url` a `/api/admin/{modulo}` + `dataSrc → res.data`.
- **No toques** `routes/web.php`, Actions ni el módulo de autenticación.

### Parte 1 — Backend

Cada `index()` que alimenta una tabla responde con el contrato oficial (no el envelope unificado):
`{ "draw": 1, "recordsTotal": ..., "recordsFiltered": ..., "data": [...] }`.

- Lee `draw`, `start`, `length`, `search[value]`, `order[0][column]`, `order[0][dir]`.
- `recordsTotal`: total sin filtros. `recordsFiltered`: total tras búsqueda/filters.
- Data: la página actual vía `Resource::collection`. Aplica los Filters/Pipeline existentes.
- **Mantén el envelope unificado `{status, message, data, errors}` sin cambios** en `store`, `update`, `destroy`, `show` y métodos personalizados — solo cambia `index()`.

### Parte 2 — Frontend

En cada módulo: `serverSide: true, processing: true, pageLength: 15`, `ajax.url` al API, `dataSrc` que solo `return res.data`, manteniendo las funciones `render*` para la presentación. Mismo patrón en todos los módulos.

**Al terminar:**

- Crea `fixes/2026-08-06-datatables-serverside.md` documentando causa raíz, módulos migrados, confirmación del envelope unificado intacto, y pruebas (paginación, búsqueda y ordenamiento en al menos 2-3 módulos).
- Actualiza la tabla "Fixes aplicados" y una nota en "Decisiones técnicas" explicando que los `index()` que alimentan DataTables usan el contrato oficial en vez del envelope unificado (y por qué: evitar traer datasets completos al navegador).
- Registra este prompt en `prompts.md` como el siguiente número correlativo.
- Prueba manualmente (o deja instrucciones claras) la navegación, búsqueda y ordenamiento en 2-3 módulos representativos. Detente al terminar y espera confirmación.

**Resultado:** Se reemplazó el contrato propio por el **contrato oficial de DataTables server-side** (`{draw, recordsTotal, recordsFiltered, data}`) mediante `App\Filters\DataTables::server()` (búsqueda global `search[value]`, `order[0][column]/dir`, corte `skip/take`). Los 16 `index()` usan el helper; el envelope unificado quedó intacto en `store/update/destroy/show`. Se corrigieron las URLs de DataTable a `/api/admin/{modulo}` y se limpiaron las llamadas crud `/admin-api/...` inválidas. Verificado: los 16 endpoints devuelven el contrato oficial, paginación (`start/length`), ordenamiento y búsqueda global funcionan; `php -l` y `node --check` OK.

---

## Prompt #7 — 2026-08-06

**Tipo:** Corrección de arquitectura (eliminar clases custom `Pipeline`/`DataTables`, usar Pipeline + paginación nativa de Laravel)

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido

**Este fix es de refactor, no de funcionalidad.** No elimines ni alteres ninguna regla de negocio existente (ej. la validación de conflicto de horario en `CreateCitaAction`, las validaciones de dependencias antes de eliminar en Owners/Species/etc.) — solo se reemplaza el mecanismo interno de filtros y de respuesta paginada por el patrón nativo de Laravel.

### Problema encontrado

Se crearon dos clases custom que no debían existir:

1. **`app/Filters/Pipeline.php`** — un wrapper propio alrededor de lo que ya resuelve `Illuminate\Pipeline\Pipeline` de forma nativa. Los Filters usaban `static function handle(Builder $query, Request $request): Builder`, que **no es el contrato real del Pipeline de Laravel** — es una firma inventada que imita el patrón pero no lo es.
2. **`app/Filters/DataTables.php`** (con método `::server()`) — una clase custom para formatear la respuesta server-side. El plugin DataTables.net (CDN, con server-side incluido) solo necesita recibir el JSON estándar (`draw`, `recordsTotal`, `recordsFiltered`, `data`) — no hace falta una clase PHP dedicada, y menos con un nombre que choca con la librería JS.

### Parte 1 — Pipeline nativo

Elimina `App\Filters\Pipeline` por completo. Convierte cada Filter existente (toda `app/Filters/**`) al contrato real de Laravel — instancia de clase, no estático, con `handle($passable, Closure $next)`: constructor inyecta `Request`, devuelve `return $next($query);`. En cada Controller reemplaza `Pipeline::apply(...)` por `app(Pipeline::class)->send($query)->through([...])->thenReturn()` usando `Illuminate\Pipeline\Pipeline`. Aplica en todos los módulos con Filters.

### Parte 2 — Paginación nativa + envelope agnóstico

Elimina `App\Filters\DataTables` por completo. Cada `index()` de Api/Admin que alimenta tabla usa `->paginate()` directo y devuelve envelope limpio agnóstico de DataTables:

`{success: true, data: Resource::collection($paginated->items()), pagination: {total, per_page, current_page, last_page, has_more}}`

El backend **no conoce** el contrato DataTables (`draw`/`recordsTotal`/`recordsFiltered`) — es responsabilidad del JS. En cada `js/pages/{modulo}.js` configura DataTables con `ajax` como **función** que traduce `{success, data, pagination}` → contrato interno de DataTables ({`draw`, `recordsTotal`=pagination.total, `recordsFiltered`=pagination.total, `data`}) y reenvía `per_page`/`page`/`search`/`sort_by`/`sort_dir`. Aplica en todos los módulos.

### Alcance

- Audita toda `app/Http/Controllers/Api/Admin/**` — todos los endpoints JSON
- `app/Filters/**` — convertir todos los Filters
- Eliminar `App\Filters\Pipeline` y `App\Filters\DataTables`
- No toques Actions ni reglas de negocio — solo el mecanismo de filtrado y respuesta paginada

**Nota de alcance (aclarada antes de codear):** el plan es internamente contradictorio — dice "No toques JS" en Alcance pero Parte 2 reescribe el JS. Decisión: aplicar el **plan completo** (reescribir JS con función `ajax`) porque cambiar el contrato backend sin tocar el JS rompería las DataTables. Además, se **conserva la búsqueda global y el ordenamiento** que el ejemplo simplificado del plan omitía, para no regresar una regresión post-Fix #4.

**Al terminar:**

- Crea `/fixes/2026-08-06-pipeline-nativo-y-datatables-response.md` documentando: confirmación de las dos clases eliminadas, lista de módulos migrados al Pipeline nativo, confirmación de que ninguna regla de negocio cambió de comportamiento, y la fecha del fix.
- Actualiza `project-map.md`: la tabla "Filters registrados" debe reflejar el nuevo contrato (`handle($passable, Closure $next)`), y agrega la decisión técnica de por qué se usa el Pipeline nativo en vez de uno propio.
- Registra este prompt en `prompts.md` como el siguiente número correlativo.
- Detente al terminar y espera confirmación antes de continuar.

**Resultado:** Se eliminaron `App\Filters\Pipeline` y `App\Filters\DataTables`. Los 8 Filters se migraron al contrato real de Laravel (instancias con `Request` inyectado, `handle($query, Closure $next)`). Los 16 `index()` de `Api/Admin/*` usan `Illuminate\Pipeline\Pipeline` (+`->paginate()` nativo) y devuelven el envelope agnóstico `{success, data, pagination}`; solo `index()` cambió — `store/update/destroy/show` conservan `{status,message,data,errors}`. Los 17 `js/pages/*.js` usan DataTables con `ajax`-función que traduce el envelope y conserva búsqueda (`search`) y ordenamiento (`sort_by`/`sort_dir`, whitelist por módulo). Verificado por tinker: Pipeline filtra (Breeds species_id=1→8/29), paginación (`per_page=2`→2/3), búsqueda (`search=Lima`→3) y ordenamiento (`sort_dir=desc`→"Sede San Isidro") OK; `php -l`, `node --check`, `view:cache` y `routes:list api/admin` OK.

---

## Prompt #8 — 2026-08-06

**Tipo:** Corrección de arquitectura (búsqueda/orden/filtros fuera de los controllers, en Actions + Filters)

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase módulo estamos
3. `project-map.md` — estado real, incluyendo lo que quedó del Fix #5 (Pipeline nativo + `List{Modulo}Action`)

**Problema encontrado:** el Fix #5 movió el listado a `List{Modulo}Action` solo en los módulos que ya tenían Filters formales (Citas). Pero hay controllers, como `OwnerController@index`, que nunca pasaron por ese patrón y siguen con lógica de negocio cruda dentro del método: armado manual de `where(...)->orWhere(...)` para búsqueda y un mapeo a mano de columnas ordenables (`$orderable[...]`) con validación de dirección de orden. Eso es lógica, no debería vivir en el controller — misma regla que ya aplicamos, sin excepciones.

### Qué hacer

Audita `app/Http/Controllers/Api/Admin/**` completo. Para cada `index()` que tenga:

- Construcción de búsqueda (`where`/`orWhere` sobre columnas)
- Mapeo de columnas ordenables + validación de dirección (`asc`/`desc`)
- Cualquier otro `if`/lógica condicional sobre el query antes de paginar

...conviértelo al mismo patrón del Fix #5: 1) búsqueda y orden se vuelven Filters de Pipeline; 2) la `List{Modulo}Action` queda como única dueña de armar la query; 3) el controller queda delgado (solo `$paginated = $action->execute($request);` + envelope `{success, data, pagination}`).

**Nota sobre Filters transversales:** si varios módulos repiten el mismo patrón de "buscar en N columnas" y "ordenar por columna mapeada", evalúa si conviene un Filter genérico reutilizable en vez de uno por módulo. Usa criterio y documenta la decisión.

### Alcance

- Audita absolutamente todos los `index()` en `app/Http/Controllers/Api/Admin/**`
- `app/Filters/**` — agregar los Filters de búsqueda/orden que falten
- `app/Actions/**` — agregar/completar el `List{Modulo}Action`
- No toques Blade; el JS no debería necesitar cambios si el envelope se mantiene igual — verifícalo

**Pedido adicional del usuario:** limpiar también `app/Http/Controllers/Admin/**` (web) — cualquier `index()` con lógica de consulta/paginación debe quedar simple; solo `Admin/MedicalRecordController@index` tenía lógica real (y el blade usa DataTable server-side, así que `$records` era lógica muerta).

**Al terminar:**

- Crea `fixes/2026-08-06-logica-suelta-controllers.md` con la tabla módulo → tenía lógica (sí/no) → Filters + Action creados, y confirmación de que el envelope no cambió, + la fecha.
- Actualiza `project-map.md`: tablas "Filters registrados" y "Actions registradas" + decisión técnica de los Filters genéricos.
- Registra este prompt en `prompts.md` como el siguiente número correlativo.
- Detente al terminar y espera confirmación.

**Resultado:** Creados los Filters genéricos `App\Filters\Shared\FiltrarPorBusqueda` y `App\Filters\Shared\OrdenarPor` (configurables por constructor) y `App\Filters\Pagos\FiltrarPorEstado`. Creadas 16 `List{Modulo}Action` (Pipeline nativo + Filters + `->paginate()`). Refactorizados los 16 `index()` de `Api/Admin/*`. Nota de Pipeline: los Filters se pasan como instancias (`new FiltrarPorX($request)`) para usar el `$request` explícito de la Action. Envelope intacto; JS sin cambios. Limpiado `Admin/MedicalRecordController@index` (lógica muerta). Verificado por tinker: Breed `species_id=1`→8, Cita `status=pendiente`→5, Invoice `pagado`→2, Payment `pagado`→6; Branch `per_page=2`→2/3, `search=Lima`→3, `sort desc`→"Sede San Isidro"; HTTP `branches` y `owners` → 200. `php -l` OK; tests: 20 fallos preexistentes e independientes (Auth/Profile, DB de test sin migrar).

---
