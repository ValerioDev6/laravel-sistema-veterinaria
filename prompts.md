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

## Prompt #9 — 2026-08-06

**Tipo:** Consolidación de UI — módulo Pacientes con tabs, sin vistas duplicadas

**Contexto del proyecto:** Sistema de gestión veterinaria en Laravel Blade + Bootstrap 5 + DataTables 1.13 (server-side vía AJAX) + SweetAlert2 + jQuery. Layout x-app-layout con sidebar y breadcrumbs. Ya existen vistas funcionales para "Especies" y "Razas" (listado + alta/edición vía SweetAlert2 o modal), cada una con su propio JS en `js/pages/species.js` y `js/pages/breeds.js`.

**Objetivo:** Consolidar el módulo "Pacientes" en un único index con tabs (nav-tabs de Bootstrap), SIN crear vistas Blade nuevas y duplicadas. Reutilizar las vistas existentes de Especies y Razas como partials/includes dentro de un tab, minimizando archivos Blade nuevos.

### Estructura esperada en `admin/pacientes/index.blade.php`

1. **Tabs de nivel módulo (Bootstrap nav-tabs):**
   - Tab "Listado" (activo por defecto): la tabla actual de pacientes.
   - Tab "Especies y Razas": debe INCLUIR el contenido ya existente de las vistas de especies y razas (`@include('admin.species.partials.table')` y `@include('admin.breeds.partials.table')`, o refactorizar el contenido actual en partials si aún no lo están), mostrando ambas tablas lado a lado (o apiladas en mobile) dentro del mismo tab-pane.

2. **No duplicar lógica JS:**
   - `js/pages/species.js` y `js/pages/breeds.js` deben seguir funcionando igual dentro del tab (mismos selectores `#table-species` / `#table-breeds`), sin reescribir su lógica AJAX/DataTable.
   - Cargarlos vía `@push('scripts')` en el index de pacientes.
   - Si las tablas usan DataTables, inicializarlas solo cuando el tab se activa (evento `shown.bs.tab` de Bootstrap) para evitar problemas de renderizado con tablas ocultas por `display:none`.

3. **Rutas:**
   - Mantener las rutas existentes `admin.species.*` y `admin.breeds.*` intactas (para los endpoints AJAX de listar/crear/editar/eliminar).
   - Quitar del sidebar el bloque "Especies y Razas" (`#sidebarSpecies`) ya que ahora vive dentro de Pacientes.

4. **Alta/edición:**
   - Mantener el mismo patrón ya usado (SweetAlert2 o modal) para crear/editar tanto Especie como Raza, sin cambios de comportamiento, solo cambia el lugar donde se muestra (dentro del tab en vez de vista independiente).

5. **Estilo:**
   - Usar el patrón nav-tabs / nav-link ya presente en el sistema de diseño (Bootstrap 5 estándar, iconos `ri-*`), consistente con el resto del template.

### Entregable esperado

- `admin/pacientes/index.blade.php` actualizado con nav-tabs "Listado" / "Especies y Razas".
- Los partials de species/breeds extraídos (si no existían) para poder incluirlos sin duplicar HTML.
- Ajuste del sidebar quitando el ítem "Especies y Razas".

**Al terminar:** registra este prompt en `prompts.md` como el siguiente número correlativo.

**Resultado:** Creados `resources/views/admin/species/partials/table.blade.php` y `resources/views/admin/breeds/partials/table.blade.php` (card + `#table-species` / `#table-breeds`), e `index` de Especies y Razas refactorizados a `@include` del partial (siguen funcionando como vistas independientes; rutas `admin.species.*` / `admin.breeds.*` intactas). `admin/pacientes/index.blade.php` ahora tiene nav-tabs "Listado" (tabla de pacientes, activo) y "Especies y Razas" (ambas tablas lado a lado, `col-lg-6` / apiladas en mobile) con `@push('scripts')` de `pacientes.js`, `species.js` y `breeds.js`. En `species.js`/`breeds.js` se añadió `inicializarCuandoVisible()`: si la tabla está en un `tab-pane` inactivo, la DataTable se inicializa en el primer `shown.bs.tab` (sin tocar la lógica AJAX/DataTable, solo difiriendo la primera inicialización). Removido del sidebar el bloque "Especies y Razas" (`#sidebarSpecies`). Verificado: `view:cache` OK, `node --check` OK en los 3 JS, rutas de Especies/Razas intactas.

---

## Prompt #10 — 2026-08-07

**Tipo:** Corrección de UI — tab "Especies y Razas" SOLO con formularios de creación (sin listado/tablas)

**Contexto:** En la vista de Pacientes (index), el tab "Especies y Razas" mostraba tablas/DataTable de Especies y Razas. Se elimina por completo esa idea: el tab debe mostrar ÚNICAMENTE los formularios de creación.

**Requerimiento:**
1. **Nueva Especie:** campo `nombre` → botón "Guardar" → POST a `admin.species.store` vía AJAX. Al guardar: SweetAlert2 de éxito, limpia el form y refresca en memoria (AJAX, sin recargar) el `<select>` "Especie" que usa el form de Nueva Mascota y el propio form de Nueva Raza.
2. **Nueva Raza:** campos `nombre` y `<select>` "Especie" (poblado desde la misma fuente `$species` del form de Paciente, sin duplicar la fuente de datos). Botón "Guardar" → POST a `admin.breeds.store` vía AJAX. Al guardar: SweetAlert2 + limpia el form.
3. **Layout:** dos cards lado a lado (apiladas en mobile), sin tabla debajo, sin botón "Ver listado". Estilo consistente (`form-control`, `btn-primary`, `invalid-feedback`).
4. **SE ELIMINA:** cualquier DataTable de especies/razas, botón "+ Nueva Especie" modal, y cualquier link/redirección a `admin.species.index` / `admin.breeds.index`.
5. Las vistas Blade de listado independientes ya no se usan (sidebar limpio); las rutas store se mantienen; los index de esos módulos quedan sin uso en el frontend pero no se borran del backend.

**Entregable:** `admin/pacientes/index.blade.php` con el tab mostrando solo los 2 formularios; JS (`species-breeds-inline.js` o similar) con el submit AJAX de ambos forms; confirmación explícita de que no quedó tabla ni link de listado.

**Resultado:** En `admin/pacientes/index.blade.php` el pane `#tabEspeciesRazas` ahora contiene solo dos cards (col-lg-6, apilables en mobile) con los formularios "Nueva Especie" (`#formNuevaEspecie` → POST `/api/admin/species`) y "Nueva Raza" (`#formNuevaRaza` → POST `/api/admin/breeds`), estilo estándar (`form-control`, `btn-primary`, `invalid-feedback`), sin tabla ni link de listado. El `<select>` "Especie" de Nueva Raza se puebla server-side desde `$species` (misma fuente del form de Nueva Mascota). Creado `public/js/pages/species-breeds-inline.js` con el submit AJAX de ambos forms (SweetAlert + reset + limpieza de validación) y `refrescarSelectsEspecie()` que recarga `GET /api/admin/species` y actualiza todos los `select[name="species_id"]` del DOM conservando la selección. `PacienteController@index` ahora pasa `"species" => $this->species()`. Se quitan del `@push('scripts')` de pacientes `species.js`/`breeds.js` (se mantienen para sus páginas independientes) y se eliminan los `@include` de los partials de tabla del tab. Sin redirección tras guardar. Verificado: `php -l` OK, `view:cache` OK, `node --check` OK. Confirmación: no quedó ninguna tabla/DataTable ni link de listado en el tab.

---

## Prompt #11 — 2026-08-07

**Tipo:** Rediseño profesional del módulo Pacientes (UI + imágenes libres por especie)

**Contexto del proyecto:** Sistema de gestión veterinaria en Laravel Blade + Bootstrap 5 + DataTables 1.13 (server-side vía AJAX) + SweetAlert2 + jQuery. El usuario pidió mejorar la UI del módulo `resources/views/admin/pacientes/` usando la skill `frontend-design`, que las especies y razas queden **separadas** (no dos CRUDs apretados en un mismo pane), y que se agreguen **imágenes gratis** para las especies.

**Requerimiento:**
1. Rediseñar profesionalmente el index de Pacientes: hero con identidad de clínica, tarjetas de estadísticas (pacientes, especies, razas), y **tabs separados** para "Pacientes", "Especies" y "Razas".
2. Cada tab de catálogo con su propia card: cabecera con título + contador, formulario de creación inline (`formNuevaEspecie`/`formNuevaRaza`, AJAX a `/api/admin/species` y `/api/admin/breeds`) y su DataTable (reutilizando `species.js`/`breeds.js`/`pacientes.js` existentes).
3. **Imágenes libres (stock) por especie:** cada especie y raza muestra una foto gratuita (Pexels) según su nombre, con fallback a icono.
4. Consistencia con el sistema (Bootstrap 5, `form-control`, SweetAlert2, validación inline `invalid-feedback`).
5. Las páginas independientes `admin/species/index.blade.php` y `admin/breeds/index.blade.php` siguen existiendo y funcionando (con sus DataTables), solo que ya no se enlazan desde el sidebar.

**Entregable:** `admin/pacientes/index.blade.php` rediseñado; helper de imágenes `public/js/helpers/species-images.js`; ajustes en `species.js`/`breeds.js` (columna Foto + hook de recarga); actualización de partials y controller (`$totales`); verificación de compilación.

**Resultado:** `admin/pacientes/index.blade.php` rediseñado por completo: hero degradado verde con acento dorado (identidad de clínica, tipografías Fraunces + Manrope), franja de 3 tarjetas de estadísticas (Mascotas/Especies/Razas desde `$totales`), y **3 tabs separados** — "Pacientes" (DataTable con foto de mascota), "Especies" (card: form inline `#formNuevaEspecie` + tabla `#table-species` con columna Foto) y "Razas" (card: form inline `#formNuevaRaza` + tabla `#table-breeds` con columna Foto). Creado `public/js/helpers/species-images.js` con `URLFotoEspecie()`/`fotoEspecie()`: mapeo de nombres de especie → foto gratuita de Pexels (verificado por HTTP: perro 1108099, gato 2061057, ave 1148820, conejo 326012, pez 128756, caballo 1904105, reptil 2600409, tortuga 8005020, roedor 175709, erizo 7030663) con `onerror` a icono paw. `species.js` y `breeds.js` agregan columna de foto y exponen `recargarEspecies()`/`recargarRazas()`; `species-breeds-inline.js` recarga la tabla y los selects tras guardar. `PacienteController@index` pasa `$totales` (Paciente::count, Species::count, Breed::count). Partials y páginas standalone de Especies/Razas actualizados (columna Foto + helper cargado). Verificado: `php -l` OK, `view:cache` OK, `node --check` OK en los 5 JS, `tinker` renderiza la vista OK (`VIEW_RENDER_OK`).

---

## Prompt #12 — 2026-08-07 (corrección de UI — revert de exceso)

**Tipo:** Revert de UI a Velzon puro. Solo formularios de creación de Especie/Raza (sin listado, sin CSS custom, sin stats, sin fotos stock).

**Contexto:** El Prompt #11 pintó el index de Pacientes con tema verde custom (fonts Fraunces/Manrope), hero, stats y 3 tabs con DataTables de Especies/Razas con fotos de Pexels. El usuario lo marcó como **basura"**: el proyecto usa la plantilla **Velzon** y, según `proyecto-veterinaria.md`/`project-map.md`, no se debe desviar del estilo Velzon (CSS custom solo excepcional). Pide: **solo UI, no borrar nada de backend**; **quitar el listado de especies/razas sin borr0 nada**; dejar **solo los formularios de creación**, agregando una **"imagen tipo"** (emoji/icono) si hay espacio en el form de la nueva especie; **no stats** de totales en esa card; **no fotos** en las tablas.

**Acciones:**
1. `admin/pacientes/index.blade.php` → estilo **Velzon** limpio (`page-title-box`, `nav-tabs nav-tabs-custom`, cards estándar, `btn btn-primary`), sin CSS custom/fuentes ajenas. Dos tabs:
   - **Listado**: DataTable de pacientes (`#table-pacientes`).
   - **Especies y Razas**: solo dos cards de formulario (`col-lg-6`) — `#formNuevaEspecie` y `#formNuevaRaza` — con **emoji "tipo"** (🐾/🐶) en el header y `input-group` con iconos `ri-*`. Sin tabla debajo.
- `PacienteController@index`: eliminado `$totales` y el import `Breed` (mi adición previa, ya no se usa; se conserva `species` para el select del form de Raza). No se quita funcionalidad.
- `species.js`/`breeds.js`: revertidas las columnas de foto (vuelta a columnas simples) y eliminados `window.recargarEspecies`/`recargarRazas`. Las páginas standalone de Especies/Razas siguen con su DataTable normal.
- Partials de las tablas de Especies/Razas: revertida la cabecera (sin columna Foto).
- `species/index.blade.php` y `breeds/index.blade.php`: retirado el `@archivo js/helpers/species-images.js` (el archivo queda en disco sin uso; no se borra).
- `species-breeds-inline.js`: sin cambios; los guards `typeof window.recargar...` evitan errores al no existir ya los hooks.

**Verificado:** `php -l` OK, `view:cache` OK, `node --check` OK en los 4 JS; `tinker` confirma que la vista tiene `#table-pacientes` + `#formNuevaEspecie`, y NO tiene `#table-species`/`#table-breeds`, ni `pv-module` (css verde), ni `totales`. No se elimina nada de backend.

---

## Prompt #13 — 2026-08-07

**Tipo:** Reorganización del index de Pacientes en 4 tabs separados + mejora de formularios (validación required, Limpiar/Regresar, autolimpieza tras guardar)

**Contexto:** El usuario pidió un index con 4 tabs, en orden: **Buscar Paciente**, **Formulario Raza**, **Formulario Especie**, **Formulario Paciente** (solo eso, sin listados de especies/razas). Además pidió en los formularios de los tabs: validación tradicional (campos required en rojo), limpia automática tras guardar, y botón **Regresar** (y **Limpiar**) además de **Guardar**.

**Cambios:**
1. `admin/pacientes/index.blade.php`: 4 tabs en el orden pedido (`#tabBusquedaPaciente`, `#tabFormRazas`, `#tabFormEspecie`, `#tabFormPaciente`, activo = Buscar Paciente). Tab "Buscar Paciente" = listado con buscador (`#busquedaPaciente`). Formularios de Raza y Especie con campo ID (disabled, "Auto", nota "se genera automáticamente"), campos `required`, alerta de obligatorios y botones **Guardar / Limpiar (reset) / Regresar** (`data-bs-toggle="tab"` a `#tabBusquedaPaciente`).
2. `admin/pacientes/partials/form-create.blade.php`: formulario de paciente sin `novalidate` (validación nativa de required) con **Guardar + Limpiar + Regresar** (Regresar paramétrico por `@include`). `create.blade.php` lo incluye con `regresarUrl = route('admin.pacientes.index')`.
3. `public/js/pages/pacientes.js`: `initForm` detecta si el form está en un `.tab-pane` → tras guardar muestra Swal y **resetea el form** (sin redirigir); en create independiente sigue redirigiendo a `/admin/pacientes`. Añadido handler `reset` que limpia validación. Expuesto `buscarPacientes()` + canjeo del input `#busquedaPaciente` con `dataTable.search()`.
4. `public/js/pages/species-breeds-inline.js`: handlers `reset` que limpian validación de ambos forms inline.

**Verificado:** `php -l` OK, `view:cache` OK, `node --check` OK (pacientes.js, species-breeds-inline.js); `tinker` confirma 4 tabs en orden correcto, botones Regresar/Limpiar presentes, sin `novalidate`, `required` presente, sin tablas de especies/razas; `create.blade.php` OK (Regresar → `/admin/pacientes`). Backend sin cambios funcionales (solo se agregó `owners` a la vista index para el formulario inline).

---

## Prompt #14 — 2026-08-10

**Tipo:** Rediseño completo del flujo de creación de Citas (pago obligatorio, disponibilidad por fecha, servicio nuevo inline)

**Contexto:** El usuario marcó el create de citas como "basura": se agendaba una cita sin pago, sin mostrar el costo del servicio, con selects planos. Rechazó la idea de "cita sin pago" ("CÓMO VAS A HACER UNA CITA SIN PAGO DIOS!!") y pidió que el pago sea la prioridad, mostrando el costo del servicio, con capacidad de crear un servicio nuevo inline (ej. "cortar uñas") si no existe en el catálogo, y un flujo con disponibilidad: **Mascota → Servicio → Fecha → Disponibilidad (vet + horas libres) → Motivo → Pago**.

**Cambios:**

1. **Backend — `StoreCitaRequest`**: reglas para `new_service` (name, category, base_price, duration_minutes, con `required_without:service_id`/`required_with`) y pago obligatorio (`payment_method` en efectivo/tarjeta/transferencia/otro + `advance_amount` min 0.01). `status` pasa a nullable (lo define el Action). Se conserva la validación de horario/cruce de citas.
2. **Backend — `CreateCitaAction`** (era "muy pobre"): ahora envuelto en `DB::transaction()`:
   - Si no llega `service_id` pero sí `new_service`, crea el `Service` primero.
   - Crea la `Cita`.
   - Genera `Invoice` (`invoiceable_type = cita`, owner del paciente, total = `base_price` del servicio, `remaining_balance`, status `pagado`/`parcial`, `issued_at = appointment_date`).
   - Genera `Payment` (amount = adelanto, método, status `pagado`, `paid_at = now()`).
   - Si el adelanto cubre el total, promueve la cita a `confirmada`.
   - Método `registrarPago` estático (llamado con `self::`) para ser compatible con `execute` estático.
3. **Backend — `ObtenerDisponibilidadAction`** (nuevo): dado `fecha`, toma `veterinarian_schedules` activos del día (`day_of_week`), genera slots de 30 min (start <= t < end) y descarta los ocupados por citas pendiente/confirmada. Agrupa por veterinario (maneja horarios mañana+tarde). Expone `veterinarian_id`, `veterinarian`, `start_time`, `end_time`, `slots`.
4. **API — `CitaController@disponibilidad`** + ruta `GET api/admin/citas/disponibilidad` (registrada antes del `apiResource` para no colisionar con `{cita}`).
5. **Web — `Admin/CitaController@create`**: pasa `services` como colección con `base_price`/`duration_minutes` (nuevo `serviciosConPrecio()`) y `categories`; se mantiene `services()` (array id→nombre) para el edit.
6. **Vista `admin/citas/create.blade.php`** (rediseñada, 6 bloques):
   - Mascota (select).
   - Servicio con **costo visible en cada opción** (`name — S/ base_price`) + opción **"＋ Crear nuevo servicio"** que despliega campos inline (nombre, categoría, precio, duración) en `#bloqueNuevoServicio`.
   - Fecha (min hoy) → al cambiarla consulta disponibilidad y puebla `#veterinarian_id` + horas libres como botones (`#contenedorHoras`).
   - Motivo.
   - **Pago (obligatorio)**: total a pagar readonly (auto-calculado del servicio), método de pago, adelanto/monto.
   - Botón "Guardar y cobrar".
7. **JS `public/js/pages/citas.js`**:
   - `recolectarDatos`: excluye `service_id` cuando vale `__nuevo__`, incluye `payment_method`, `advance_amount` y `new_service[`*`]` si aplica.
   - `pintarErroresValidacion`: mapea errores `new_service.name` → `[name="new_service[name]"]`.
   - Bloque `formCrearCita`: `actualizarTotal()`, toggle del bloque de servicio nuevo, `cargarDisponibilidad()` (GET disponibilidad + render de botones de hora), validación adelanto ≤ total, submit AJAX a `/admin/citas`.

**Verificado:** `php -l` OK en Request/Actions/Controllers; `php artisan view:cache` OK; `node --check` OK en `citas.js`; `route:list` muestra `admin.api.citas.disponibilidad`; tinker: disponibilidad devuelve slots por vet sin duplicados, crear cita con servicio nuevo + pago completo genera Invoice `pagado` (saldo 0) + Payment, y con adelanto parcial genera Invoice `parcial` (saldo = total − adelanto) + Payment; render de vista OK (`BLOQUE_NUEVO_OK`, `DISPO_OK`, `PAGO_OK`). Datos de prueba eliminados al final.

---

## Prompt #15 — 2026-08-10

**Tipo:** Rediseño adaptativo del create de Citas (flujo: veterinarios primero con check/ocupado, preview de mascota, pago como prioridad)

**Contexto:** El usuario marcó el create de citas como "basura" de nuevo, pero esta vez por el diseño: pidió que sea **adaptable/responsive** (no tanto espacio libre, que se adapte solo), que **primero esté la búsqueda del veterinario** (al filtrar por fecha, si un veterinario está disponible que salga un check para aplicarlo; si está ocupado que salga tachado/"ocupado"), que al seleccionar la mascota aparezca algo de su data (foto), que los servicios sigan manejándose como principal, y que **el pago sea la prioridad** (la gente no suele pagar completo; lo que importa es que haya adelanto/pago). El usuario aclaró que el precio del servicio "ya no importa", continuar.

**Cambios:**

1. **Backend — `ObtenerDisponibilidadAction`**: ahora devuelve **todos** los veterinarios con horario activo ese día (antes saltaba los ocupados). Cada entrada incluye `disponible` (bool: quedan slots libres) y `slots` (vacío si ocupado). El frontend decide cómo mostrarlos (check vs tachado).
2. **Web — `Admin/CitaController@create`**: pasa además `pacientesData` (nuevo método privado con `with(["species","breed","owner"])`: id, name, photo, species, breed, gender, weight, owner) para el preview de la mascota sin petición extra.
3. **Vista `admin/citas/create.blade.php`** (layout adaptativo a 12 columnas, sin `col-lg-8`):
   - Header con badge "Cita con pago".
   - **Fecha** → dispara disponibilidad.
   - **Veterinarios disponibles**: grid responsive `col-md-4 col-lg-3` de cards; cada card con nombre, horario y badge Disponible/Ocupado; los disponibles tienen botón "✓ Aplicar"; los ocupados salen tachados (`text-decoration-line-through`) con opacidad y botón deshabilitado "Sin horas". Al aplicar se muestra bloque de horas libres (`#bloqueHoras` + `#contenedorHoras`).
   - **Mascota** con preview: al seleccionar se muestra foto (o icono paw) + especie/raza/género/peso + dueño (`#bloquePreviewMascota`).
   - **Servicio** (principal) con costo visible + opción "＋ Crear nuevo servicio" inline.
   - **Motivo**.
   - **Pago (prioridad, obligatorio)**: total readonly auto-calculado, método, adelanto.
4. **JS `public/js/pages/citas.js`** (bloque `formCrear`):
   - Eliminados los selects de veterinario/hora en favor de cards: `renderVetCard()` (disponible/ocupado), `bindVetCards()`, `aplicarVet()` (resalta card seleccionada, setea `veterinarian_id`, muestra `#bloqueHoras`), `renderHoras()` con botones de hora (setean `appointment_time`).
   - Preview de mascota en el `change` del select: usa `window.citasPacientesData` inyectado por la vista.
   - Se mantiene: `actualizarTotal()` (costo del servicio o del nuevo), validación adelanto ≤ total, submit AJAX con `recolectarDatos` (excluye `__nuevo__`, envía `new_service[*]`, `payment_method`, `advance_amount`).

**Verificado:** `php -l` OK (Actions/Controllers), `php artisan view:cache` OK, `node --check` OK en `citas.js`; tinker: disponibilidad devuelve `disponible=SI/NO` correcto (vet con todas las horas tomadas → `disponible=NO`, slots=[]; vet libre → `disponible=SI`), render de vista con todos los bloques (`contenedorDisponibilidad`, `contenedorHoras`, `bloquePreviewMascota`, `citasPacientesData`, pago OK) y 11 mascotas en `pacientesData`. Datos de prueba eliminados.

---

## Prompt #16 — 2026-08-10

**Tipo:** Actualización del edit de Citas al mismo diseño del create (disponibilidad, preview de mascota, servicio nuevo, pago)

**Contexto:** El usuario pidió que el editar de citas (`/admin/citas/{id}/edit`) sea igual al create rediseñado (los dos formularios son "casi iguales"), es decir: flujo con veterinarios disponibles por fecha (check/ocupado), preview de mascota, servicio como principal con costo y opción de crear servicio nuevo inline, y bloque de pago (obligatorio). Solo que en editar se permite cambiar el estado de la cita y todo llega precargado.

**Cambios:**

1. **Backend — `UpdateCitaRequest`**: ahora espeja a `StoreCitaRequest`: reglas para `new_service` (name, category, base_price, duration_minutes) y pago obligatorio (`payment_method` + `advance_amount` min 0.01); `status` nullable. Mantiene `validarHorario` con exclusión de la propia cita (`id !=`).
2. **Backend — `UpdateCitaAction`**: envuelto en `DB::transaction()`:
   - Crea `Service` nuevo si llega `new_service` sin `service_id`.
   - Actualiza la cita.
   - **Actualiza o crea la `Invoice`** (cita) con total = `base_price`, saldo y status `pagado`/`parcial`.
   - **Actualiza o crea el `Payment`** de esa invoice (monto, método, status `pagado`).
   - Si el adelanto cubre el total, promueve la cita a `confirmada`.
3. **Web — `Admin/CitaController@edit`**: pasa `pacientesData`, `serviciosConPrecio`, `categories` y `invoice` (con `payments`) para precargar el bloque de pago; ya no pasa `veterinarians`. Se elimina el método privado `services()` (sin uso).
4. **Vista `admin/citas/edit.blade.php`**: misma estructura adaptativa que el create (12 columnas, badge "Cita con pago"), con:
   - Fecha y **Estado** (pendiente/confirmada/completada/cancelada) precargados.
   - Disponibilidad de veterinarios + horas, con `veterinarian_id`/`appointment_time` ocultos precargados.
   - Mascota, Servicio (con `@selected`), bloque "＋ Nuevo servicio", preview de mascota.
   - Pago precargado desde la invoice: total readonly, método (`@selected` según payment), adelanto (`value` del payment).
   - `window.citaEditarInit = { fecha, veterinario, hora }` para preseleccionar el veterinario y su hora en el JS.
5. **JS `public/js/pages/citas.js`**: refactorizado a una única función `initCitaForm(form)` compartida por create y edit:
   - Detecta edición por `form.id` y usa `PUT /admin/citas/{id}` (con `form.dataset.id`) o `POST`.
   - En edición: al cargar disponibilidad llama `preseleccionarEnEdicion()` (aplica el veterinario guardado y resalta su hora `btn-primary`).
   - `renderPreviewMascota()`, `actualizarTotal()`, validación adelanto ≤ total, `recolectarDatos` (excluye `__nuevo__`, envía `new_service[*]`, `payment_method`, `advance_amount`, y `status` si existe).

**Verificado:** `php -l` OK (Request/Action/Controllers), `php artisan view:cache` OK, `node --check` OK en `citas.js`; tinker: render del edit con datos precargados (fecha, `payment_method` efectivo seleccionado, adelanto 100, total 200, `contenedorDisponibilidad`, `citaEditarInit`), `UpdateCitaAction` actualiza hora/motivo e invoice/payment (pago 150 tarjeta → saldo 50), y se restauró la cita 37 a su estado original (hora 09:00, pago 100 efectivo, saldo 100). Datos de prueba restaurados.

---
## Prompt #17 — 2026-08-10

**Tipo:** Filtro de disponibilidad por fecha — veterinarios sin horario deben salir como "No disponible" (tachado), con lógica de query builder / SQL

**Contexto:** El usuario detectó que el filtro de disponibilidad del create/edit de citas era incorrecto: al elegir fecha salía "Disponible" en veterinarios que realmente no lab<br>boran ese día (los 3 veterinarios tenían exactamente el mismo horario Lun–Vie 09–13/15–19 en BD, por eso dr.ramos "siempre parecía disponible"). Pidió:
- Que cada veterinario tenga su horario realista propio (Lun–Vie o Lun–Sáb, horarios distintos: 07:00–21:00, 08:00–18:30, 09:00–19:00).
- Que la consulta sea más compleja usando query builder / SQL (`DB::table`) y valide de verdad si el médico está o no disponible ese día.
- Que un veterinario sin horario el día filtrado salga como **"No disponible"** (tachado, `sin_horario`), no que desaparezca ni que diga solo "Ocupado".
- Se usó como referencia (solo lógica, no estructura) un ejemplo TypeScript con `queryRunner`/`HorarioCitas` que crea cita + historial + pago en transacción; nuestra implementación tiene menos tablas.

**Cambios:**

1. **`app/Actions/Citas/ObtenerDisponibilidadAction.php`** (reescrito):
   - Usa `User::role("Veterinario")->where("is_active", true)` para partir de **todos** los veterinarios (no solo los que tienen schedule).
   - Horarios del día con **`DB::table("veterinarian_schedules")`** (query builder raw) agrupados por `veterinarian_id`.
   - Cruza con citas ocupadas (`pendiente`/`confirmada`) de la fecha.
   - Devuelve por cada vet: `horario` ("07:00 – 21:00" o "Sin horario este día"), `slots`, y un nuevo campo `estado` con 3 valores: `disponible` (tiene slots libres), `ocupado` (tiene horario pero todas las horas tomadas), `sin_horario` (no se le asignó día).
   - `generarSlots` sigue generando slots de 30 min excluyendo ocupados; `sinHorario()` arma el arreglo con `start_time`/`end_time` null.
2. **`database/seeders/VeterinarianScheduleSeeder.php`** (reescrito): perfiles de horario por índice del vet:
   - dr.torres: Lun–Vie 09:00–13:00 y 15:00–19:00.
   - dr.ramos: **Lun–Sáb** 08:00–12:30 y 14:00–18:30.
   - dr.paredes: Lun–Vie 07:00–12:00 y 16:00–21:00.
   - Se limpiaron los horarios viejos (que eran idénticos para los 3) antes de reseedar.
3. **`public/js/pages/citas.js`** — `renderVetCard` ahora usa el campo `estado` y muestra **3 estados**: badge "Disponible" (verde + botón Aplicar), "Ocupado" (rojo tachado + "Sin horas"), **"Sin horario"** (gris tachado + "No disponible", card `opacity-50`); muestra `vet.horario` guardado por el backend.

**Verificado:**
- tinker: reseed de horarios = dr.torres 10, dr.ramos 12 (incluye sábado), dr.paredes 10 registros.
- tinker `execute('2026-08-24')` (lunes): 3 disponibles con sus horarios distintos (slots 15/18/20).
- `2026-08-29` (sábado): dr.paredes y dr.torres `sin_horario`, solo dr.ramos disponible.
- `2026-08-30` (domingo): los 3 `sin_horario`.
- HTTP real con `php artisan serve`: `GET /api/admin/citas/disponibilidad?fecha=...` devuelve el trio con `estado`/`horario`/`slots` correctos (ok para lunes, sábado y domingo).

---
## Prompt #17c — 2026-08-10 (Fix #7: diagnóstico + fix citas duplicadas en mismo horario)

**Tipo:** Fix de bug — diagnóstico primero, causa confirmada antes de corregir

**Prompt de referencia:** `fixes/07_fix.md`

**Síntoma reportado:** al registrar una cita para un veterinario en una fecha/hora donde ya existe otra cita activa del mismo veterinario, el sistema "deja pasar" el registro — no bloquea como debería.

**Diagnóstico (ordenado según el fix doc):**

1. **Paso 1 — ¿Se duplica en BD? NO.** Se reprodujo el caso y se revisó `citas`: solo queda 1 registro. El backend rechaza con 422 en ambas capas: `StoreCitaRequest`/`UpdateCitaRequest` (regla `validarHorario`) y `ValidarDisponibilidadCita::execute()` dentro de la transacción de `Create/UpdateCitaAction`. Log temporal en la Action confirmó que el FormRequest bloquea antes de llegar a la Action para el par ocupado.
2. **Paso 2B — Bug de UI (causa raíz).** El 422 llegaba al frontend pero `public/js/config/ajax.js` hacía solo `reject(error)` **silencioso**, y `citas.js` `.catch` pintaba inline en `appointment_time`, que es `<input type="hidden">` (invisible). El usuario creía que el sistema "dejaba pasar".
3. **Falso positivo aclarado:** la cita 39 no era el bug; la cita 37 está a las `07:00:00`, así que `09:00` de ese día estaba libre. La cita 39 fue creada por tests del prompt #17b.

**Fix aplicado (solo UI, causa confirmada):**

- `public/js/config/ajax.js`: todo 422 muestra SIEMPRE SweetAlert2 con `response?.message` || primer `errors` || fallback; título según método (DELETE = "No se pudo eliminar", resto = "No se pudo guardar"); luego `reject(error)` para que la página siga pintando inline. Single source of truth para todos los módulos.
- Se eliminaron los Swal redundantes de `.catch(422)` en los deletes de 11 módulos (breeds, vaccine-types, usuarios, vacunas, citas, cirugias, medicines, pacientes, services, species, owners) para evitar doble popup (el Swal global ya los cubre).
- **Paso 2A NO aplicó** (no se normalizó formato de hora en backend): no había bug de backend confirmado; el diagnóstico dio que la validación ya funciona.
- Log temporal **quitado** de `ValidarDisponibilidadCita.php`.

**Verificado:**
- `node --check` OK en `ajax.js` y 11 `pages/*.js`; `php -l` OK en `ValidarDisponibilidadCita.php` (log removido).
- Backend ya verificado en #17b (POST duplicado → 422 "El veterinario ya tiene una cita a esa hora."; no inserta).
- Documentación creada en `fixes/2026-08-06-citas-duplicadas.md`; `project-map.md` actualizado (decisión técnica 422 en capa AJAX + fila de fix).
- Verificación visual del Swal en navegador: pendiente de confirmación manual del usuario.

---
## Prompt #17b — 2026-08-10 (corrección del filtro + validaciones en store)

**Tipo:** Bugfix del filtro de disponibilidad + validaciones de disponibilidad dentro del store

**Contexto:** El usuario insistió (a tono confuso, con ejemplo TypeScript/queryRunner de referencia) que el filtro seguía mal: "la consulta tiene que hacer consulta y validaciones en store", "el filtro me trae los veterinarios pero no me trae si ya está ocupado o no es el día". Pedía (1) query builder/SQL real con joins que marque ocupación por slot, y (2) que las validaciones de disponibilidad se ejecuten también en la creación/edición de la cita, no solo en el Request. Quería el patrón del ejemplo TS: consultas complejas y guardados múltiples (cita + pago) en transacción con chequeos.

**Cambios:**

1. **Nuevo `app/Actions/Citas/ValidarDisponibilidadCita.php`**: validación reutilizable con `DB::table` que (a) verifica que el vet tenga horario activo ese día de la semana (start <= time < end) y (b) que no exista otra cita activa (pendiente/confirmada) del mismo vet en la misma fecha+hora, ignorando `$ignorarCitaId` (para update). Lanza `ValidationException` con el mensaje correspondiente.
2. **`CreateCitaAction`**: llama `ValidarDisponibilidadCita::execute($data)` dentro de la transacción (antes de crear service/cita).
3. **`UpdateCitaAction`**: llama `ValidarDisponibilidadCita::execute($data, $cita->id)` dentro de la transacción.
4. **`ObtenerDisponibilidadAction`** (reescrito con query builder real):
   - Vets vía `DB::table('users')->join('model_has_roles')->join('roles')` (rol Veterinario, activos).
   - Schedules del día con `DB::table('veterinarian_schedules')` agrupados por vet.
   - Ocupadas con `DB::table('citas')` ese día (pendiente/confirmada).
   - Por cada vet devuelve `slots_detalle` (cada slot con `hora`, `ocupado`, `disponible`), además de `slots` (solo libres), `horario`, `estado` (disponible/ocupado/sin_horario).
   - **Bug encontrado y corregido**: la columna `appointment_time` es `TIME`, el query builder raw devuelve `"09:30:00"` (con segundos) mientras las claves se arman con `"09:30"` — por eso la ocupación no matcheaba. Se normaliza con `Carbon::parse(...)->format("H:i")` en el action y con `whereRaw('TIME(appointment_time) = ?')` en la validación.
   - `VeterinarianScheduleSeeder` ya quedó con perfiles distintos por vet (dr.torres Lun–Vie 09–13/15–19; dr.ramos Lun–Sáb 08–12:30/14–18:30; dr.paredes Lun–Vie 07–12/16–21).

**Verificado:**
- `php -l` OK en action, seeder y validación; `node --check` OK en citas.js.
- tinker: `2026-08-24` → dr.torres `slots_detalle` marca `09:00` como ocupado (cita 37), 15 libres; domingo → 3 `sin_horario`; sábado → solo dr.ramos disponible.
- `ValidarDisponibilidadCita`: rechaza 09:00 ocupada, acepta 09:30 libre, y con `ignorarCitaId=37` permite el mismo horario (update).
- HTTP real: `GET /api/admin/citas/disponibilidad?fecha=2026-08-24` muestra `ocupados=['09:00']` en dr.torres; `POST /api/admin/citas` con hora ocupada → 422 "El veterinario ya tiene una cita a esa hora"; hora fuera de horario (14:30) → 422 correcto; hora libre (15:30) → 201. Cita temporal de prueba (id 38) eliminada después (citas quedan 9: seeds 1–8 + cita 37).

---
## Prompt #17d — 2026-08-10 (Calendario de Citas profesional con Drawer lateral)

**Tipo:** Mejora de UI (FullCalendar enriquecido) + endpoint dedicado de calendario

**Contexto:** El usuario consideró el calendario de citas "pobre": los eventos eran solo `pet — vet`, al hacer clic se abría un Swal simple con servicio/estado/motivo, y la data venía del index paginado (10 por página → no se veían todas las citas). Pidió algo similar a su ejemplo NestJS/TypeORM (`CalendarioService.calendar()` con `extendedProps` ricos: mascota, dueño, veterinario, costo, notas, estado) y un **drawer lateral izquierdo** al hacer clic en la cita, con info de la cita y solo el campo **Estado** editable (guardado inline). Además pidió mover el CDN de FullCalendar al layout global y registrar el prompt.

**Cambios:**

1. **`resources/views/layouts/app.blade.php`** — CDN de FullCalendar v6.1.11 movido al layout global (junto a ajax.js), ya no se carga por vista.
2. **Backend (endpoint dedicado):**
   - Nuevo `app/Actions/Citas/ObtenerCitasCalendarioAction.php` — devuelve **todas** las citas (sin paginar) con `paciente.species/breed/owner`, `veterinarian`, `service`, `medical_records`.
   - Nuevo `app/Http/Resources/CitaCalendarioResource.php` — shape nativo de FullCalendar: `id`, `title` (mascota), `start`/`end` (fecha+`H:i`, fin = hora + `duration_minutes` del servicio), `allDay`, `color` (por veterinario), y `extendedProps` con `status`, `status_label`, `veterinarian`, `veterinarian_id`, `vet_color`, `service`, `reason`, `cost` (service.base_price), `day` (nombre en español), `hora_atencion`, `notes` (primer `medical_record.notes`), `pet` (name/species/breed/owner/phone) y `edit_url`.
   - `Api/Admin/CitaController@calendario` + ruta `GET api/admin/citas/calendario`.
3. **`resources/views/admin/citas/calendar.blade.php`** — se quitó el CDN de la vista (ahora global); se agregó el **Offcanvas izquierdo** (`#offcanvasCita`, Bootstrap) con info de la cita (Veterinario, Mascota, Hora de Atención, Día, Costo, Servicio, Razón, Notas Médicas), un `<select>` de **Estado** y botones "Guardar cambios" + "Editar Cita" (enlaza a `edit_url`).
4. **`public/js/pages/citas-calendar.js`** — reescrito: consume `GET /api/admin/citas/calendario` (todas las citas en una petición); `eventClick` abre el drawer (`bootstrap.Offcanvas`) rellenando la info y el estado actual; "Guardar cambios" hace `PATCH /api/admin/citas/{id}/estado` (endpoint `cambiarEstado` ya existía) → cierra el drawer y `refetchEvents()`. Swal de éxito/error lo muestra la capa AJAX global.
5. **Ajustes de diseño (feedback del usuario sobre la 1ra versión):**
   - **Color por veterinario** (no por estado): paleta de 10 colores fija en el Resource (`vet_color`), misma para todas las citas de un vet; el JS usa `vet_color` (sin duplicar paleta en front).
   - **Drawer a la DERECHA** (`offcanvas-end`, no `offcanvas-start`).
   - **CSS propio para estilizar FullCalendar** (`@push('styles')` en calendar.blade.php): cabecera con botones redondeados + accent #405189, eventos con borde izquierdo grueso y sombra, hover con elevación, celdas/días suaves, day-today resaltado, tooltip nativo (`eventMouseEnter` → `title` con mascota · hora · vet) y **leyenda de veterinarios** (chips con su color) bajo el calendario (idempotente en refetch).

**Verificado:**
- `php -l` OK en Action, Resource, Controller y rutas; `node --check` OK en citas-calendar.js; `view:cache` OK.
- `route:list` confirma `api/admin/citas/calendario` y `admin.citas.calendar` (web, sidebar) sin conflicto.
- tinker: el action trae las 9 citas; `CitaCalendarioResource::collection()->resolve()` produce el shape completo (ej. cita 1: Rocky, 2026-08-06T09:30 → 10:00, color #f43f5e por vet 2 dr.torres, S/60, día jueves, dueño María Quispe Huamán, edit_url ok; vet 3 → #10b981, vet 4 → #f59e0b).
- Render del blade: `view('admin.citas.calendar')` contiene `offcanvas-end`, `offcanvasCita`, `leyendaVets`, estilos `fc-*` y el JS de la página.
- HTTP real (con `php artisan serve`): `GET /api/admin/citas/calendario` devuelve todas las citas con `vet_color`; `PATCH /api/admin/citas/2/estado` cambió pendiente→confirmada→(restaurada a pendiente) con respuesta `{status,message,data}`. BD quedó intacta (9 citas, cita 2 pendiente).

---
## Prompt #18 — 2026-08-11 (UI del módulo Vacunas replicando Citas)

**Tipo:** Mejora de módulo — Vacunas con filtros, disponibilidad de veterinarios y pago, igual que Citas

**Contexto:** El usuario pidió rediseñar el módulo Vacunas para replicar el patrón de Citas: (1) index con filtros (mascota, especie, veterinario, estado de pago, fecha desde–hasta), (2) formulario con disponibilidad de veterinarios (fecha → vets → horas libres/ocupadas) y (3) sección de Pago en el formulario (método, total, adelanto).

**Decisión de diseño:** para replicar el selector de horas de Citas se agregó `vacunas.vaccination_time` (TIME) y `vaccine_types.base_price` (decimal 10,2 default 0) vía migración `2026_08_11_000001_add_time_and_price_to_vacunas_and_vaccine_types.php`; dump SQL y seeders actualizados (7 tipos con precios 35–70; 6 vacunas con hora).

**Cambios:**

1. **Disponibilidad unificada:** `ObtenerDisponibilidadAction` ahora une citas activas + vacunas (con `vaccination_time`) como horas ocupadas del veterinario. Nueva `app/Actions/Vacunas/ValidarDisponibilidadVacuna.php` (horario activo del día + conflicto con cita pendiente/confirmada + conflicto con otra vacuna, con `ignorarVacunaId` para update).
2. **Requests:** `StoreVacunaRequest`/`UpdateVacunaRequest` con `vaccination_time`, `payment_method`, `advance_amount` obligatorios y `after()` con `ValidarDisponibilidadVacuna`.
3. **Actions [TX]:** `CreateVacunaAction` (vacuna + medical_record + reminder + Invoice + Payment) y `UpdateVacunaAction` (TX + `registrarPago` upsert de invoice/pago; estado `pagado`/`parcial`/`pendiente` según adelanto, total = `base_price` del tipo).
4. **Filtros:** `app/Filters/Vacunas/` (Busqueda, Especie, Veterinario, EstadoPago, Fecha). `ListVacunasAction` con pipeline completo + eager load `paciente, user, vaccine_type, invoice, invoice.payments`.
5. **Resource:** `VacunaResource` con `species`, `vaccination_time`, `vaccine_price`, `payment_status`, `payment_total`, `payment_paid`.
6. **API:** `Api/Admin/VacunaController` con método `disponibilidad` + `show`; ruta `GET api/admin/vacunas/disponibilidad` (`admin.api.vacunas.disponibilidad`) antes del apiResource.
7. **Frontend:** `Admin/VacunaController` pasa veterinarians/species/paymentStatuses (index) y pacientesData/vaccineTypes/invoice (create/edit). Vistas `index` (formFiltrosVacunas + columna Pago) y `create`/`edit` (bloque disponibilidad con vet-card + horas, preview mascota, sección Pago). `public/js/pages/vacunas.js` reescrito (DataTable serverSide + filtros + `initVacunaForm`).
8. **CRUD vaccine-types:** `base_price` en Requests/Actions/Resource/vistas + `vaccine-types.js`.

**Bug preexistente corregido de paso:** `UpdateVaccineTypeRequest` línea 21 usaba `{$this->vaccineType->id}` pero el parámetro de ruta es `{vaccine_type}` (snake) → `$this->vaccineType` era null → "Attempt to read property 'id' on null" al actualizar por HTTP. Corregido a `{$this->route('vaccine_type')?->id}`.

**Verificado:**
- `php -l`, `node --check`, `view:cache`, `route:list` OK.
- HTTP real (`php artisan serve --port=8123`): disponibilidad 2026-05-15 marca 09:30 dr.torres ocupada (vacuna Rocky); POST vacuna con pago → 201 invoice `pagado`; 422 por hora ocupada con otra vacuna; 422 por hora ocupada con cita; PUT → parcial; filtros todos OK; CRUD vaccine-types create/update/delete con precio OK (tras el fix).
- Limpieza: vacuna de prueba y tipos de prueba (9, 10) eliminados → BD final 7 tipos, 6 vacunas, 12 invoices, 9 citas intactas.
- Servidor detenido al cerrar.

**Corrección posterior (update roto):** el update de vacunas fallaba (422 "The vaccination date field must be a date after or equal to today.") porque `UpdateVacunaRequest` tenía `after_or_equal:today` y las 6 vacunas del seed tienen fechas pasadas. Fix: la regla de `vaccination_date` en update es una Closure que permite conservar la fecha original pasada; el `min` del input en `edit.blade.php` usa la fecha original; la vacuna de Kiara se movió de domingo (día sin horario) a viernes en `VacunaSeeder` y en la BD; y se reordenaron los scripts de `edit.blade.php` (datos `window.vacunasPacientesData`/`vacunaEditarInit` antes de cargar `vacunas.js`) para que el preview de mascota y la preselección de disponibilidad apliquen al cargar la página. Verificado por HTTP: PUT con fecha pasada original → 200 para las 6 vacunas; PUT a otra fecha pasada → 422; vista edit 200. BD restaurada limpia.

**Ampliación (nuevo tipo de vacuna inline):** el usuario pidió que Vacunas permita registrar un nuevo tipo de vacuna desde el formulario, replicando `new_service` de Citas. Fix: `vaccine_type_id` en los Requests pasó a `required_without:new_vaccine_type` + nullable; se agregó `new_vaccine_type` (name, base_price, species_id); `CreateVacunaAction`/`UpdateVacunaAction` crean el `VaccineType` en la TX si no viene id; `Admin/VacunaController` pasa `species`; las vistas create/edit tienen la opción `＋ Crear nuevo tipo de vacuna` (`__nuevo__`) con bloque de nombre/precio/especie; `vacunas.js` maneja el toggle y calcula el total con el precio del nuevo tipo. Verificado por HTTP: POST/PUT con `new_vaccine_type` → 201/200 (invoice con el precio nuevo); 422 si falta tipo y nuevo tipo; 422 si falta el nombre. BD restaurada limpia (7 tipos, 6 vacunas, 12 invoices, 6 payments, 9 citas).

---

## Prompt #19 — 2026-08-11 (Calendario unificado: Citas + Vacunas + Cirugías)

**Tipo:** Mejora de UI — nuevo calendario general que muestra los 3 tipos clínicos

**Contexto:** El usuario consideró que el calendario existente (Citas → Calendario) solo mostraba citas, cuando vacunas y cirugías también tienen fecha. Pidió un calendario donde se vean las tres cosas, cada tipo con su propio color, sin tocar el código de los módulos de Citas/Vacunas/Cirugías (sus CRUD y el calendario de citas quedan intactos).

**Decisiones de diseño (aprobadas en brainstorming):**
- Ubicación: nuevo ítem de menú principal **"Calendario"** (entrada directa, sin dropdown) → `GET /admin/calendario` (`admin.calendario.index`).
- Colores: 1 color fijo por tipo — Citas `#405189` (azul), Vacunas `#10b981` (verde), Cirugías `#f59e0b` (naranja). Leyenda con 3 chips bajo el calendario.
- Clic en evento: offcanvas único con detalle (mascota, especie, raza, dueño, teléfono, veterinario, fecha/hora, detalle según tipo, estado, notas) + botón **Editar** que navega a la edición de ese registro. Sin cambio de estado inline (eso ya vive en cada sección).

**Cambios:**

1. **`app/Actions/Calendario/CalendarioAction.php`** (nuevo) — devuelve las 3 colecciones sin paginar con sus relaciones: citas (`paciente.species/breed/owner`, `veterinarian`, `service`, `medical_records`), vacunas (ídem con `user`, `vaccine_type`) y cirugías (`Surgiere` con `user`, `medical_records`).
2. **`app/Http/Resources/CalendarioEventResource.php`** (nuevo) — recibe modelo + `$tipo` ("cita"|"vacuna"|"cirugia") y produce eventos FullCalendar: `id` prefijado (`cita-`, `vacuna-`, `cirugia-`), `title` (mascota), `start`/`end`, `allDay` (vacunas sin hora → allDay), `color` y `extendedProps` (tipo/tipo_label/color, mascota, veterinario, detalle, status/status_label, notas, `edit_url`). Constructor con parámetro extra (los JsonResource de Laravel lo soportan con `new CalendarioEventResource($m, "cita")`).
3. **Controllers nuevos:** `Admin/CalendarioController@index` (vista) y `Api/Admin/CalendarioController@index` (une los 3 tipos vía `collect()->merge()` en un arreglo plano de eventos).
4. **Rutas:** `GET admin/calendario` (web, `admin.calendario.index`) y `GET api/admin/calendario` (`admin.api.calendario`).
5. **Vista `admin.calendario.index`** — copia el estilo FullCalendar de citas pero con `#calendar-general`, leyenda de 3 tipos y offcanvas `offcanvasEvento` con badge de tipo + `<dl>` informativo + botón Editar.
6. **`public/js/pages/calendario.js`** (nuevo) — carga `ajax.get("/admin/calendario")` (el helper `public/js/config/ajax.js` ya antepone `baseURL="/api"` → `GET /api/admin/calendario`), asigna color por tipo, `eventClick` abre el offcanvas (Intl `es-PE` para fecha/hora, muestra "Próxima dosis" solo en vacunas, etiqueta de detalle según tipo), tooltip nativo con tipo · mascota · vet.
7. **Sidebar** — ítem "Calendario" (icono `ri-calendar-2-line`) al final de la sección Clínica, tras Historial Clínico.

**Fuera de alcance:** CRUD de citas/vacunas/cirugías intactos; `Citas → Calendario` (citas-calendar.js + CitaCalendarioResource) se mantiene como está.

**Verificado:**
- `php -l` en Action, Resource, 2 controllers y rutas; `node --check` en calendario.js; `view:cache` OK.
- `route:list` confirma `admin.calendario.index` y `admin.api.calendario` sin conflicto con `admin.api.citas.calendario`.
- HTTP real (`php artisan serve --port=8123`): login 302, vista `/admin/calendario` 200; `GET /api/admin/calendario` → 19 eventos (9 citas, 7 vacunas, 3 cirugías); ej. cita Rocky `#405189` con servicio "Consulta general" y edit_url `/admin/citas/1/edit`; vacuna Rocky `#10b981` "Séxtuple canina" → `/admin/vacunas/1/edit`; cirugía Thor `#f59e0b` "Esterilización" → `/admin/cirugias/1/edit`.
- Servidor detenido al cerrar; BD intacta (no se modificó nada).

**Corrección posterior (404 `api/api/admin/calendario`):** al abrir el calendario el usuario reportó "The route api/api/admin/calendario could not be found". Causa: en `calendario.js` se llamaba `ajax.get("/api/admin/calendario")` pero el helper `ajax` (`public/js/config/ajax.js`) ya antepone `baseURL="/api"` → resultaba `/api/api/admin/calendario`. Fix: la llamada pasó a `ajax.get("/admin/calendario")` (idéntico al patrón de `citas-calendar.js` con `/admin/citas/calendario`). Verificado con `node --check`; el resto de la página no cambió.

**Corrección posterior (drawer sin guardar/cancelar + todo azul):** el usuario reportó dos problemas: (1) el drawer había perdido la actualización de estado — quedó solo con botón "Editar" — y debía volver a permitir Guardar/Cancelar; (2) en el calendario todo salía del mismo color azul. Fix:
- Drawer: se eliminó el botón "Editar" y se añadió un **select de estado** por tipo (cita: pendiente/confirmada/completada/cancelada; vacuna: pendiente/parcial/pagado/anulado; cirugía: pendiente/en_proceso/completada/cancelada) + botones **Guardar** y **Cancelar**. Guardar hace `PATCH` según tipo: `/admin/citas/{id}/estado`, `/admin/cirugias/{id}/estado` (existían) y **nuevo** `PATCH /admin/vacunas/{id}/estado-pago` (endpoint `cambiarEstadoPago` con `CambiarEstadoPagoVacunaAction`, que reusa `RegistrarPagoAction`/`AnularPagoAction` según el estado destino).
- Colores: la causa era que el tema Velzon fuerza `.fc-event-title` azul con `!important` y FullCalendar usa su azul por defecto si el color inline no gana. Fix: cada evento recibe `className` (`ev-cita`/`ev-vacuna`/`ev-cirugia`) y la vista define CSS con `!important` para el fondo (Citas `#405189`, Vacunas `#10b981`, Cirugías `#f59e0b`).
- `CalendarioAction` ahora eager-loada `invoice` en vacunas y `eventoVacuna` expone `status`/`status_label` (estado de pago de la factura).
- Verificado por HTTP: vacuna 1 pendiente→pagado→pendiente (invoice restaurada con saldo 35 y pago anulado); cita 2 y cirugía 3 ida y vuelta de estado; 19 eventos con colores y className correctos. `php -l`, `node --check`, `view:cache` OK. BD restaurada (se eliminó el único pago anulado de prueba creado en la sesión).

---

## Prompt #20 — 2026-08-11 (Rediseño del index de Tipos de Vacuna: búsqueda manual)

**Tipo:** Mejora de UI — alinear `vaccine-types/index` a la convención del proyecto

**Contexto:** El usuario pidió rediseñar `resources/views/admin/vaccine-types/index.blade.php` siguiendo la convención de las demás listas (ej. Vacunas): formulario de **búsqueda manual** con input + botones **Filtrar/Limpiar**, quitando la barra de búsqueda automática del DataTable. "Nada más que eso".

**Cambios:**

1. **Vista `index.blade.php`:** se agregó `<form id="formFiltrosVaccineTypes">` con input `busquedaVaccineTypes` (icono `ri-search-line`, placeholder "Nombre o especie…") + botones Filtrar (`ri-filter-line`) y Limpiar (`ri-eraser-line`), colocado entre el card-header y la tabla (mismo patrón de `admin/vacunas/index`).
2. **`public/js/pages/vaccine-types.js`:** el DataTable ahora usa `searching: false, lengthChange: false, info: false` (sin buscador/paginación info nativos); se agregó `getFilters()` que inyecta `search` desde `terminoBusqueda`; el input dispara `dataTable.ajax.reload()` con debounce de 350 ms; Enter se bloquea; el submit del form y el botón Limpiar (que hace `form.reset()` + limpia el término) recargan la tabla.

**Sin cambios de backend:** `ListVaccineTypesAction` ya filtraba por `search` vía `FiltrarPorBusqueda(['name'])`; aquí también coincide con la especie porque el resource devuelve el nombre de especie.

**Verificado:** `node --check`, `view:cache`, `php -l` OK. HTTP real (`php artisan serve`): vista 200 con el form presente (4 coincidencias de ids en el HTML); `GET /api/admin/vaccine-types?search=rabia` → 2 (Rabia canina, Rabia felina); `search=felina` → 3 (Leucemia/Rabia/Triple felina); sin search → 7. Servidor detenido.

---

## Prompt #21 — 2026-08-11 (Módulo Cirugías replicando Vacunas/Citas: filtros del index + pago y factura en el create)

**Tipo:** Módulo completo (backend + vistas + JS)

**Contexto:** El usuario pidió adaptar el módulo de Cirugías (`resources/views/admin/cirugias/`) para que sea similar a Vacunas y Citas: el **index** con filtros por **nombre de mascota, especie, veterinario, estado de pago y fecha desde/hasta**, y el **create** con selección de mascota (con preview), form de cirugía y bloque de **Pago/Factura obligatorio**. Aclaración del usuario: "pago obligatorio como Vacunas" (método + adelanto mínimos, y el **total se ingresa manualmente** porque la cirugía no tiene catálogo/precio base). "Nada más que eso".

**Cambios:**

1. **Backend — Filters nuevos en `app/Filters/Cirugias/`:** `FiltrarPorBusquedaCirugia` (mascota, veterinario, `surgery_type`), `FiltrarPorEspecieCirugia` (por `paciente.species_id`), `FiltrarPorVeterinarioCirugia`, `FiltrarPorEstadoPagoCirugia` (por invoice `surgiere`), `FiltrarPorFechaCirugia` (`surgery_date_from/to`).
2. **Backend — `ListCirugiasAction`:** pipeline con los 5 filters + `OrdenarPor` + eager load `paciente`, `user`, `invoice`, `invoice.payments`.
3. **Modelo `Surgiere`:** nueva relación `invoice()` (`hasOne Invoice` con `invoiceable_type='surgiere'`).
4. **Backend — `CirugiaResource`:** agrega `species_id`, `species`, `surgery_time`, `payment_status`, `payment_total`, `payment_paid`.
5. **Backend — Requests `Store/UpdateCirugiaRequest`:** agregan `total` (required, min 0), `payment_method` (required, enum) y `advance_amount` (required, min 0.01).
6. **Backend — Actions `Create/UpdateCirugiaAction`:** `registrarPago()` genera/actualiza la `Invoice` polimórfica `surgiere` (total, saldo, status `pagado`/`parcial`/`pendiente`) y su `Payment`, igual que Vacunas. `DeleteCirugiaAction` ya bloqueaba cirugías con historial médico.
7. **Backend — nuevo `CambiarEstadoPagoCirugiaAction` + endpoint `PATCH api/admin/cirugias/{cirugia}/estado-pago`** (ruta `admin.api.cirugias.estado-pago`), replica el de Vacunas (pagado↔pendiente↔anulado mediante `RegistrarPagoAction`/`AnularPagoAction`).
8. **Web — `Admin/CirugiaController`:** el `index` pasa `veterinarians`, `species`, `paymentStatuses`; `create`/`edit` pasan `pacientesData` (preview de mascota) e `invoice` (con `payments`) en edición.
9. **Vistas:** `index` con form de filtros (buscar, veterinario, especie, estado de pago, fecha desde/hasta) + columna **Pago**; `create` y `edit` rediseñadas con preview de mascota y bloque de pago (total manual, método, adelanto), "Guardar y cobrar".
10. **JS `public/js/pages/cirugias.js`:** `recolectarFiltros()`/`getFilters()` para la DataTable server-side (debounce 350 ms, Filtrar/Limpiar); `initPreviewMascota()` desde `window.cirugiasPacientesData`; `recolectarDatos()` incluye `total`, `payment_method`, `advance_amount`; columna Pago (badge) y dropdown de cambio de estado de pago en acciones.

**Verificado por HTTP:** login 200; `GET /api/admin/cirugias` → contrato `{success, data, pagination}` con `payment_status`; filtros: `payment_status=parcial`→2, `species_id=1`→2, `search=Rocky`→1; vista index 200 con filtros; create/edit 200 con bloque de pago. `PATCH estado-pago` cirugía 2 → `pagado` (payment_total 150, payment_paid 150) y restaurada a `parcial` (saldo 75, 1 pago tarjeta). `POST` create con total 200/adelanto 80 → 201 `parcial`; `PUT` update → `pagado` 250/250. `DELETE` → 422 (regla de historial médico); datos de prueba eliminados manualmente. BD restaurada: 13 invoices, 7 payments, 3 cirugías. `php -l` OK (17 archivos), `view:cache` OK, `node --check` OK.

## Prompt #21b — 2026-08-11 (Corrección post-revisión: DataTable cirugías + tabs show de paciente)

**Tipo:** Bugfix

**Contexto:** Tras la revisión, el usuario reportó dos errores: (1) warning de DataTables en `admin/cirugias` — "Requested unknown parameter 'estado' for row 0, column 6"; y (2) en `admin/pacientes/12` los 3 tabs del show de paciente fallaban con "Call to undefined relationship [veterinarian] on model [App\Models\MedicalRecord/Vacuna]".

**Cambios:**

1. **`public/js/pages/cirugias.js`** — `datosCargados()` ahora incluye la clave `estado: renderEstado(c.status)` (badge con color + etiqueta traducida). La columna del index declaraba `{ data: "estado" }` pero el mapeo retornaba `status`, por lo que DataTables no encontraba el parámetro.
2. **`app/Http/Controllers/Api/Admin/PacienteController.php`** — métodos `records()`, `vacunas()` y `cirugias()`: `with("veterinarian")` → `with("user")` y `$modelo->veterinarian?->username` → `$modelo->user?->username`, porque los modelos `MedicalRecord`, `Vacuna` y `Surgiere` definen su relación como `user()`. La respuesta mantiene la clave `veterinarian` (no se toca `paciente-ficha.js`).

**Verificado por HTTP:** `node --check cirugias.js` y `php -l PacienteController.php` OK; `GET /api/admin/pacientes/12/{records,vacunas,cirugias}` → 200 con `veterinarian`; DataTable de cirugías sin warning. Nota: OPcache (`revalidate_freq=180`) puede retener la versión antigua del controlador por ~3 min.

## Prompt #21c — 2026-08-11 (Bloque de disponibilidad en el create/edit de Cirugías, replicando Vacunas/Citas)

**Tipo:** Mejora de UI/UX — formulario de Cirugías con selector de fecha → veterinarios (tarjetas con horario y disponibilidad) → horas libres, como Vacunas/Citas.

**Contexto:** El usuario reportó que el create de Cirugías "no se parece en nada" al de Citas/Vacunas: faltaba el broker de disponibilidad del médico (cuando seleccionas fecha ves su horario y si está disponible o no). "Corrige eso".

**Cambios:**

1. **`app/Actions/Cirugias/ObtenerDisponibilidadCirugiaAction.php`** (nuevo): replica `ObtenerDisponibilidadAction` (veterinarios con rol, horarios del día, slots de 30 min) pero la agenda **ocupada** une **citas + vacunas + cirugías activas** (status != cancelada).
2. **API**: método `disponibilidad()` en `Api/Admin/CirugiaController` + ruta `GET api/admin/cirugias/disponibilidad` (`admin.api.cirugias.disponibilidad`) **antes** del `apiResource`.
3. **`app/Actions/Cirugias/ValidarDisponibilidadCirugia.php`** (nuevo): valida (a) horario activo del vet ese día, (b) no cita activa a esa hora, (c) no vacuna a esa hora, (d) no otra cirugía activa a esa hora (ignora la cirugía actual). **En edición**, si fecha/hora/vet no cambiaron respecto a la cirugía original, se permite guardar tal cual (falta de datos del seed con fechas fuera del horario actual, igual que el fix de vacunas). Lanza `ValidationException`.
4. **`StoreCirugiaRequest` / `UpdateCirugiaRequest`**: se agrega `surgery_time` (`date_format:H:i`), `surgery_date` pasa a fecha, y `after()` que llama `ValidarDisponibilidadCirugia` (update pasa el id de la ruta robustamente: `$param = $this->route("cirugia"); $id = is_object($param) ? $param->id : (int) $param;`).
5. **Actions `Create/UpdateCirugiaAction`**: nuevo `armarFechaHora()` combina `surgery_date` (Y-m-d) + `surgery_time` (H:i) en el datetime `surgery_date`; `issued_at` de la invoice usa esa fecha.
6. **Vistas `admin/cirugias/{create,edit}.blade.php`**: replanteadas igual que Vacunas — **fecha** (disparador) → **bloque de disponibilidad** (`sinDisponibilidad`, `bloqueDisponibilidad` con tarjetas `vet-card` que muestran horario + badge Disponible/Ocupado/Sin horario + botón Aplicar) → **bloque de horas libres** → hidden `veterinarian_id` y `surgery_time`. Previews de mascota con foto, sección Pago, botón "Guardar y cobrar". En edición se pasa `window.cirugiaEditarInit = {veterinario, hora}` y se actualiza el input `surgery_date` a tipo date con el valor original.
7. **`public/js/pages/cirugias.js`**: se reemplaza la parte create/edit por `initCirugiaForm()` (mirror de `initVacunaForm`): `cargarDisponibilidad()`, `renderVetCard()`, `bindVetCards()`, `aplicarVet()`, `renderHoras()`, `preseleccionarEnEdicion()` (usando `window.cirugiaEditarInit`), validación adelanto ≤ total y submit que envía `surgery_date` + `surgery_time` + `veterinarian_id`.

**Verificado por HTTP** (servidor 127.0.0.1:8000, sesión carlos.torres):
- `GET /api/admin/cirugias/disponibilidad?fecha=2026-08-18` → 3 vets con `horario`, `estado`, `slots` (dr.torres 09:00–19:00, slots 16, etc.).
- `POST` crear con fecha+hora válida → 201 (fecha `2026-08-18 10:00`, pago parcial); hora **fuera de horario** (22:00) → 422 "fuera del horario"; hora **ocupada por otra cirugía** → 422 "ya tiene una cirugía a esa hora".
- `PUT` de la misma cirugía manteniendo su fecha/hora → 200 (self-check ignora); cirugía seed del sábado sin horario → 200 manteniendo fecha; cirugía a hora conflictiva → 422.
- Vistas create/edit → 200 con bloque de disponibilidad y `cirugiaEditarInit` presente.
- BD restaurada tras pruebas (cirugía 3 devuelta a `2026-08-01 11:00`, 13 invoices, 7 payments, 3 cirugías). `php -l`, `node --check`, `view:cache` OK.

---

## Prompt #22 — 2026-08-11 (Index de Historial Médico con cards y tabs por tipo, sin DataTable)

**Tipo:** Rediseño de UI del index de Historial Médico — cards clickeables + tabs Citas/Vacunas/Cirugías.

**Contexto:** El usuario pidió mejorar el listado de `admin/medical-records`: mantener el filtro por mascota, pero al seleccionarla cargar su historial en **tarjetas (cards)** con **tabs por tipo** (Citas/Vacunas/Cirugías), en vez de la DataTable genérica con su auto-búsqueda y "Showing 1 to 10 entries". El show se deja igual ("déjalo como está"). Acordado en brainstorm: `Citas` = event_type `consulta`; los registros `otro` solo salen en **Todos**.

**Cambios:**

1. **`resources/views/admin/medical-records/index.blade.php`**:
   - Eliminada la DataTable (`#table-medical-records`) y el form de filtrado por submit.
   - Select de mascota (`#filtro_pet_id`) que **dispara con `change`** (no submit).
   - Bloque (`#bloqueHistorial`, oculto por defecto) con **4 tabs** `nav-tabs-custom`: Todos / Citas (`ri-calendar-check-line`) / Vacunas (`ri-syringe-line`) / Cirugías (`ri-scissors-2-line`), cada uno con badge de contador (`#count-*`).
   - Contenedor `#estadoVacio` como empty state "Selecciona una mascota para ver su historial médico".
   - `<style>` con `.medical-card:hover` y `.notas-clamp` (máx 3 líneas con `-webkit-line-clamp`).
2. **`public/js/pages/medical-records.js`** (index):
   - Eliminado el bloque DataTable (`cargarDatos`, `construirDataTable`, `getFiltros`, handler submit).
   - `MAPA_TIPOS` (consulta→Cita badge info, vacuna→Vacuna success, cirugia→Cirugía primary, otro→Otro secondary) y `TIPOS_TABS = {todos, citas:consulta, vacunas:vacuna, cirugias:cirugia}`.
   - `renderTarjeta()` (card clickeable con link al show, badge de tipo, fecha `event_date`, vet, notas clamp) y `renderVacio()`.
   - `pintarTab(tipo, eventos)`: filtra en cliente por `event_type` (todos = sin filtro, incluye `otro`), pinta el badge de contador y las cards en grid `col-md-6 col-xl-4`.
   - `cargarHistorial(petId)`: `GET /api/admin/medical-records?pet_id=X` (mismo endpoint, filtro `FiltrarPorPet` ya existente), muestra el bloque de tabs y esconde el empty state.
   - `$("#filtro_pet_id").on("change")` carga/resetea; `$("#tabsHistorial").on("shown.bs.tab", "a[data-tipo]")` repinta el tab al cambiarlo.
   - Sin cambios en el bloque show/create (recetas, adjuntos, eliminar) — el archivo JS sigue sirviendo a esas vistas.

**Verificado:**
- `node --check medical-records.js` OK; `php -l` del blade OK; `view:cache` OK.
- HTTP (150: index 200 con tabs `#tabsHistorial`, `#filtro_pet_id`, `#estadoVacio`, 4 `data-tipo`, 0 `table-medical-records`). OPcache requería ~1 min para refrescar la vista compilada vieja (revalidate_freq=180).
- API `pet_id=12` (Chent) → 2 registros (vacuna + cirugía) con `event_type`, `event_date`, `veterinarian`, `notes`, `show_url` correctos.

---

## Prompt #22b — 2026-08-11 (Citas agendadas en el historial médico del index)

**Tipo:** Corrección + mejora de datos — las citas agendadas de la mascota ahora se muestran en el historial médico del index.

**Contexto:** El usuario reportó que una mascota con citas, cirugías y vacunas mostraba bien cirugías y vacunas, pero **las citas no salían**. La causa raíz: el tab "Citas" filtraba `medical_records` por `event_type=consulta`, y esa mascota no tenía un medical record de consulta — pero sí tenía citas agendadas en la tabla `citas` (ej. Chent tiene la cita #41 confirmada). Pedido: "si tiene citas, también deberían cargar ahí; en Todos con prefijo citas".

**Cambios:**

1. **`app/Actions/Citas/ListCitasAction.php`**: se agrega `new FiltrarPorPet($request)` (reutilizando `App\Filters\MedicalRecords\FiltrarPorPet`, que filtra `pet_id`) al pipeline → `GET /api/admin/citas?pet_id=X` ya devuelve solo las citas de esa mascota.
2. **`public/js/pages/medical-records.js`** (index):
   - `cargarHistorial(petId)` ahora usa `Promise.all` con **dos llamadas**: `GET /api/admin/medical-records?pet_id=X` + `GET /api/admin/citas?pet_id=X&per_page=100`, uniéndolas normalizadas con `aEventoHistorial()` (records → `{tipo, fecha, notas, url}`; citas → `{tipo:"cita", fecha:appointment_date, notas:reason, url:edit_url}`) y **ordenadas por fecha desc**.
   - `MAPA_TIPOS` agrega `cita` (mismo label/badge "Cita" que `consulta`); `TIPOS_TABS.citas` pasa a ser **`["consulta","cita"]`** para mostrar tanto medical records de consulta como citas agendadas; `pintarTab` filtra por `Array.isArray(tiposTab) ? tiposTab.includes(e.tipo) : e.tipo === tiposTab`.
   - `renderTarjeta` lee campos normalizados (`tipo/fecha/vet/notas/url`) en vez de los nombres crudos del resource.

**Verificado:**
- `node --check medical-records.js` OK; `php -l` de la Action OK; `view:cache` OK.
- HTTP `GET /api/admin/citas?pet_id=12&per_page=100` → cita #41 (Chent, `2026-08-24 07:00`, confirmada).
- Lógica simulada en node: mascota con 1 vacuna + 1 cirugía + 1 cita → **Todos** = 3 (cirugía 26/08, vacuna 25/08, cita 24/08), **Citas** = cita, **Vacunas** = vacuna, **Cirugías** = cirugía.
