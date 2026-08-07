# Fix #5 — Eliminar Pipeline y DataTables custom, usar Pipeline nativo y paginación de Laravel

**Tipo:** Fix de arquitectura (refactor de plumbing), sin tocar reglas de negocio

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/05_fix.md`

---

## Confirmación: clases custom eliminadas

- **`App\Filters\Pipeline`** — eliminada por completo. No hay referencias restantes (`rg` limpio).
- **`App\Filters\DataTables`** — eliminada por completo. No hay referencias restantes (`rg` limpio).

---

## Filtros convertidos al contrato real del Pipeline de Laravel

Cada Filter ahora es una **instancia** inyectada con `Request` y firma
`handle($passable, Closure $next)` (antes era `static handle(Builder, Request): Builder`, una firma inventada):

```php
class FiltrarPorVeterinario
{
    public function __construct(protected Request $request) {}

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("veterinarian_id")) {
            $query->where("veterinarian_id", $this->request->integer("veterinarian_id"));
        }
        return $next($query);
    }
}
```

Convertidos:
- `Citas/FiltrarPorVeterinario`, `Citas/FiltrarPorFecha`, `Citas/FiltrarPorEstado`
- `Facturas/FiltrarPorEstado`, `Facturas/FiltrarPorRangoFecha`, `Facturas/FiltrarPorOwner`
- `Breeds/FiltrarPorSpecies`
- `MedicalRecords/FiltrarPorPet`

## Uso del Pipeline nativo en los controllers

Reemplazado `Pipeline::apply(...)` por el `Illuminate\Pipeline\Pipeline` real:

```php
$query = app(Pipeline::class)
    ->send(Cita::with(["paciente", "veterinarian", "service"]))
    ->through([FiltrarPorVeterinario::class, FiltrarPorFecha::class, FiltrarPorEstado::class])
    ->thenReturn();
```

## Migración de `index()` a paginación nativa + envelope limpio

Cada `Api/Admin/*Controller::index()` alimentado por DataTable usa `->paginate()` y devuelve un
envelope agnóstico de DataTables: `{ success, data, pagination }`.

```json
{
  "success": true,
  "data": [ ...page actual vía Resource... ],
  "pagination": {
    "total": 120,
    "per_page": 15,
    "current_page": 1,
    "last_page": 8,
    "has_more": true
  }
}
```

El backend **no conoce** el contrato DataTables (`draw/recordsTotal/recordsFiltered`); esos campos
se reconstruyen en el JS.

### Lista de módulos migrados (16)

Branch, Species, Breed, Service, VaccineType, Medicine, Owner (con `withCount`), Paciente, User,
VeterinarianSchedule, Cita, Vacuna, Cirugia, MedicalRecord, Invoice, Payment.

- Con Pipeline nativo: **Breeds** (`FiltrarPorSpecies`), **Citas** (`FiltrarPorVeterinario/Fecha/Estado`),
  **MedicalRecords** (`FiltrarPorPet`), **Invoices** (`FiltrarPorEstado/RangoFecha/Owner`).
- Payments conserva su filtro inline de `status` (no tenía un Filter class propio).
- Envelope unificado `{status, message, data, errors}` en `store/update/destroy/show` y métodos
  personalizados **intacto** (confirmado: 57 coincidencias `"status" => true`, ver Parte 2).

## Parte 2 — JS: `ajax` como función (traducción en el cliente)

El JS de cada listado ahora configura DataTables con **`ajax` como función** (no URL directa),
traduciendo el envelope limpio del backend al formato interno de DataTables server-side:

```js
ajax: function (data, callback) {
    const params = {
        per_page: data.length,
        page: Math.floor(data.start / data.length) + 1,
    };
    if (data.search && data.search.value) params.search = data.search.value;
    if (data.order && data.order.length) {
        params.sort_by = data.order[0].column;
        params.sort_dir = data.order[0].dir;
    }
    ajax.get(url, params).then((res) => {
        callback({
            draw: data.draw,
            recordsTotal: res.pagination.total,
            recordsFiltered: res.pagination.total,
            data: res.data,
        });
    });
},
```

Notas:
- Se conservan la **búsqueda global** y el **ordenamiento por columna** que existían en Fix #4,
  reenviándolos como `search` / `sort_by` + `sort_dir` (el backend hace whitelist de columnas
  ordenables por módulo). Esto va un paso más allá del ejemplo literal del prompt, que no los
  incluía, para no degradar funcionalidad de duplicados (se confirmó con el autor).
- Las transformaciones de presentación (badges, montos, labels, `foto`, URLs) se mantienen
  dentro del `callback` (moviendo el mapper que antes estaba en `dataSrc`).
- Rutas del `ajax` a la API: `ajax.get("/admin/{modulo}", ...)` (el helper `public/js/config/ajax.js`
  ya antepone `/api`). En las llamadas crud POST/eliminar se mantiene `/api/admin/...` directo.

JS tocados (DataTables con `ajax` as-a-function): branches, species, breeds, services, vaccine-types,
medicines, owners, pacientes, users, veterinarian-schedules, vacunas, cirugias, citas, invoices
(facturas + pagos), medical-records.

## Confirmación de que ninguna regla de negocio cambió

- `CreateCitaAction` / validación de conflicto de horario: **sin cambios** (no se tocó `app/Actions/**`).
- Validaciones de dependencias antes de eliminar (Owners/Species/etc.): **sin cambios**.
- Envelope `{status, message, data, errors}` en `store/update/destroy/show` y métodos tipo
  `cambiarEstado`/`anular`/`toggleStatus`: **intacto** (57 usos de `"status" => true`).
- Solo se reemplazó el mecanismo interno de filtrado (Pipeline nativo) y de respuesta paginada
  (`paginate()`); las reglas de negocio no se tocaron.

## Pruebas realizadas

- `php -l` OK en los 16 `Api/Admin/*Controller` y los 8 Filters.
- `node --check` OK en los 17 `public/js/pages/*.js`.
- `view:cache` OK; `route:list api/admin` OK (83 rutas).
- Tinker:
  - Breed `species_id=1` → total 8 (29 en total) → Pipeline filtra.
  - Cita `status=pendiente` → 5; Invoice `status=pagado` → 2; MedicalRecord `pet_id=1` → 1.
  - Branch: `per_page=2` → 2 items, `total=3`; `search=Lima` → filtered 3; `sort_by=1&sort_dir=desc` →
  primer nombre "Sede San Isidro" (orden correcto).
  - Los 16 endpoints responden `{success, data, pagination}` con `total` sano.
- Envelope intacto: 57 `"status" => true`.

---

## Motores de cambio

- Eliminadas: `app/Filters/Pipeline.php`, `app/Filters/DataTables.php`.
- Convertidos: 8 Filters a contrato `handle($passable, Closure $next)` (instancia + Request inyectado).
- 16 `app/Http/Controllers/Api/Admin/*Controller.php`: `index()` con Pipeline nativo (donde
  corresponde) + `->paginate()` + envelope `{success, data, pagination}` con `search`/`sort_by`/`sort_dir`.
- 17 `public/js/pages/*.js`: DataTables con `ajax` función que traduce `{success, data, pagination}`
  y reenvía `search`/`order`.