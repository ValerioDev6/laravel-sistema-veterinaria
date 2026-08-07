# Fix #3 — Pipeline de filtros incompleto + listados sin paginación server-side + auditoría de reglas de negocio

**Tipo:** Mejora backend/frontend (pipeline de filtros + DataTable server-side) + auditoría de reglas de negocio

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/03_fix.md`

---

## Alcance

Fix estrictamente sobre la capa API/Admin + JS de las páginas de listado. **No** se tocó auth (Breeze/Spatie) ni `routes/web.php`. Se amplió el alcance (aprobado) para incluir JS/Blade y paginación server-side real con contrato propio `{ data, meta }`.

---

## Parte 1 — Pipeline de filtros solo existía en Citas

### Qué se encontró

`project-map.md` registraba el pipeline de filtros (`App\Filters\Pipeline::apply`) únicamente en Citas (fase 5) y Facturas (fase 7). El resto de módulos con filtros reales en su UI (Breeds filtrando por especie, MedicalRecords filtrando por mascota) no usaban el pipeline: sus `index` devolvían todo sin filtrar o filtraban de forma ad-hoc.

### Qué se corrigió exactamente

Se aplicó el pipeline `Pipeline::apply($query, $filters, $request)` (clase ya existente) a los módulos cuyo frontend filtra:

- **Breeds** — nuevo `app/Filters/Breeds/FiltrarPorSpecies.php` (filtra por `species_id`). Usado por el select dinámico de razas del create/edit de pacientes y por el filtro de `pacientes.js`/`breeds.js`.
- **MedicalRecords** — nuevo `app/Filters/MedicalRecords/FiltrarPorPet.php` (filtra por `pet_id`). Usado por `medical-records.js` a través del formulario `#formFiltros`.

Son los únicos dos filtros con uso real en el frontend de los listados convertidos a server-side; Citas y Facturas ya tenían sus filters.

---

## Parte 2 — Paginación server-side con contrato propio `{ data, meta }`

### Qué se encontró

Cada `index` de los `Api/Admin/*Controller` respondía con `Resource::collection($query->get())` (todo de una vez). El frontend inicializaba DataTables en modo client-side cargando todo el arreglo. Con datasets grandes esto es ineficiente (más aún con `with()` eager loading) e inviable para filtrar/ordenar en el servidor.

### Qué se eligió y por qué

Se descartó Yajra (dependencia extra) y se definió un **contrato propio**: respuesta JSON `{ data: [...], meta: { current_page, last_page, per_page, total } }`, con el backend leyendo los parámetros server-side de DataTables (`start`/`length`).

### Qué se corrigió exactamente

1. **`app/Filters/DataTables.php`** (nuevo helper reutilizable) — `DataTables::paginate($query, $request, $defaultLength = 15)`:
   - Lee `length` (default 15, sanitizado a ≥1) y `start` (≥0) tal como los envía DataTables server-side.
   - Calcula `page = floor(start / length) + 1` y ejecuta `$query->paginate($length, ["*"], "page", $page)`.
   - Devuelve `[LengthAwarePaginator, meta{ current_page, last_page, per_page, total }]`.

2. **Los 16 `Api/Admin/*Controller`** (`index`) — cada uno sustituye su colección plana por:
   ```php
   [$paginator, $meta] = DataTables::paginate($query, $request);
   return response()->json([
       "status"  => "success",
       "message" => "Listados obtenidos",
       "data"    => XResource::collection($paginator->items()),
       "meta"    => $meta,
       "errors"  => null,
   ]);
   ```
   Se conservó el `with()` / `withCount()` / `orderBy()` previo de cada uno. Se usa `Resource::collection($paginator->items())` y **no** `collection($paginator)` porque el segundo anida el `meta` de Laravel dentro de `data`, rompiendo el contrato.

3. **16 JS de páginas** (`public/js/pages/*.js`) — conversión de DataTable client-side (`data:` + `clear().rows.add()`) a **server-side** (`serverSide: true, processing: true, pageLength: 15`), con:
   ```js
   ajax: {
       url,
       data: function (d) { Object.assign(d, getFiltros("#formFiltros")); }, // cuando hay filtros
       dataSrc: function (res) {
           res.recordsTotal = res.meta ? res.meta.total : 0;
           res.recordsFiltered = res.meta ? res.meta.total : 0;
           return res.data.map((x) => ({ /* campos mapeados */ }));
       },
   }
   ```
   Las transformaciones de presentación (`renderAcciones`, `renderEstado`, labels, badges, `S/`/`$` montos, `foto`) se mantienen dentro del `dataSrc`/funciones `datosCargados`. Cualquier cambio de página/orden/filtro re-dispara la petición server-side.

Archivos JS convertidos a server-side: `citas`, `owners`, `pacientes`, `vacunas`, `cirugias`, `breeds`, `species`, `services`, `medicines`, `vaccine-types`, `veterinarian-schedules`, `usuarios`, `medical-records`, `invoices` (facturas + pagos). (Total de `serverSide` en todos los listados.)

### Sobre la función `construirDataTable()`

En `medical-records.js` e `invoices.js` la función previa `construirDataTable()` creaba una DataTable client-side antes de `cargarDatos()`, lo que impedía activar `serverSide`. Se eliminó y ahora la propia función `cargarDatos()`/`cargarPagos()` inicializa el DataTable con `serverSide` la primera vez y solo hace `ajax.reload()` en llamadas posteriores.

---

## Parte 3 — Auditoría de reglas de negocio en Actions

### Qué se encontró (corrección al supuesto del prompt)

El prompt asumía que la regla de conflicto de agenda de Citas vivía en `CreateCitaAction`. **Verificado:** NO está en la Action (`CreateCitaAction`/`UpdateCitaAction` solo asignan `created_by_user_id` y hacen create/update). La regla real vive en los Form Requests:

- `app/Http/Requests/Citas/StoreCitaRequest.php` y `app/Http/Requests/Citas/UpdateCitaRequest.php`, método `after()` → `validarHorario()`: cruza contra `VeterinarianSchedule` (día de semana + rango horario activo) y detecta otra cita del mismo vet/fecha/hora en estado `pendiente`/`confirmada` (Update excluye la propia). Es una regla cross-record legítima.

### Módulos evaluados con conflicto potencial de agenda/recurso

| Módulo | Regla evaluada | Resultado |
|--------|----------------|-----------|
| Citas | Vet sin cita duplicada fecha/hora + dentro de horario de atención | Ya existía (en Form Request `after()`, no en Action como asumía el prompt) |
| Cirugías | Conflicto de bloqueo de quirófano / vet / hora | **No aplica**: `surgery_date` es solo **fecha** (sin hora). La cirugía se ata opcionalmente a una `cita_id` (que sí cumple la regla de las citas). Inventar un conflicto por "misma fecha" sobre-vendería una distinción irrelevante; una clínica puede tener varias cirugías el mismo día. Se deja la regla de agenda en la cita que la origina |
| Vacunas | Conflicto de bloqueo por vet | **No aplica**: `vaccination_date` es solo fecha, sin hora de consulta; es un acto ambulatorio. La colisión de agenda relevante ya la valida la cita asociada. |

### No se agregaron reglas inventadas

Conforme a la premisa del prompt de no inventar lógica sin sentido clínico, no se agregó bloqueo por horario para cirugías/vacunas (no registran hora). Se documenta que **no requieren validación cruzada adicional** en este estado del esquema.

### Delete de Owners

`app/Actions/Owners/DeleteOwnerAction` ya valida que el propietario no tenga `pacientes()` (lanza `ValidationException`). Correcto; no se cambió.

### Hallazgo extra — `edit_url` roto en `MedicalRecordResource`

Al verificar el endpoint server-side de medical-records, `MedicalRecordResource` construía `edit_url => route("admin.medical-records.edit", ...)` pero esa ruta web no existe (`admin.medical-records.*` solo define `index`, `create` y `show` — el historial se edita desde la vista `show`). El `route()` lanzaba `RouteNotFoundException`, rompiendo el `index` de medical-records. Como el JS de la lista solo usa `show_url` (y el botón de eliminar), se eliminó el campo muerto `edit_url`. Fix dentro del alcance (módulo convertido a server-side).

---

## Motores de cambio

- `app/Filters/DataTables.php` (nuevo, helper paginación server-side).
- `app/Filters/Breeds/FiltrarPorSpecies.php` (nuevo).
- `app/Filters/MedicalRecords/FiltrarPorPet.php` (nuevo).
- 16 `app/Http/Controllers/Api/Admin/*Controller.php` (usa `DataTables::paginate` + `meta`).
- 13 `public/js/pages/*.js` listados nuevos a server-side + `medical-records.js`/`invoices.js` (sin `construirDataTable`).
- `Breeds`/`MedicalRecords` controllers: aplican `Pipeline::apply`.
- `app/Http/Resources/MedicalRecordResource.php` (eliminado `edit_url` muerto).

*(Sin cambios en `routes/web.php` ni en la Auth.)*

## Estado de la carga de vistas / sintaxis

- `php -l` OK en los 16 Api/Admin controllers; lint OK en `DataTables.php`, `Pipeline.php`, `FiltrarPorSpecies.php`, `FiltrarPorPet.php`.
- `node --check` OK en los 16 `js/pages/*.js` tocados.
- `php viewCache` / render de vistas OK (no se tocó Blade).
- Verificación de alineación Resource ↔ JS (subagente): **todas las columnas mapeadas existen** en los Resources y en las relaciones cargadas por cada controller (breeds→species, usuarios→branch+roles, schedules→user+day_label, invoices→owner+remaining_balance, medical-records→pet_name/header, vacunas→vaccine_type, etc.). Sin discrepancias.