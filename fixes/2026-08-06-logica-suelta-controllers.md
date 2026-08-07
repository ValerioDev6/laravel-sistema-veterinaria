# Fix #6 — Lógica de búsqueda/orden fuera de los controllers (mover a Actions + Filters)

**Tipo:** Fix de arquitectura — continuación directa del Fix #5, mismo criterio aplicado a todos los módulos

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/06_fix.md`

---

## Problema resuelto

Los `index()` de `Api/Admin/*` (16 módulos) tenían **lógica de negocio cruda** dentro del controller:
construcción manual de `where(...)->orWhere(...)` para la búsqueda global, un mapeo a mano de columnas
ordenables (`$orderable[...]`) y la validación de dirección (`asc`/`desc`). Eso es lógica, no debe vivir
en el controller — misma regla ya aplicada en Fix #5, sin excepciones.

---

## Decisión de diseño: Filters de búsqueda/orden **genéricos compartidos**

Se auditaron los 16 `index()`. Todos repetían **exactamente el mismo patrón**, variando solo:

- las columnas de búsqueda, y
- el mapa de columnas ordenables + el orden por defecto.

Por eso, siguiendo el criterio que el prompt permite ("si el patrón varía poco entre módulos, hazlo
genérico"), se crearon **2 Filters transversales reutilizables** en `app/Filters/Shared/` en vez de 32
Filters casi idénticos (uno por módulo):

| Filter compartido | Qué hace | Configuración por constructor |
|---|---|---|
| `Shared/FiltrarPorBusqueda` | `where(fn($q) => orWhere(col, like, %search%)...)` sobre la lista de columnas | `array $columns` |
| `Shared/OrdenarPor` | mapea `sort_by`/`sort_dir` a una columna SQL con whitelist + default | `array $orderable`, `array $default` |

Los Filters de dominio ya existentes (Citas `FiltrarPorVeterinario/Fecha/Estado`, Facturas
`FiltrarPorEstado/RangoFecha/Owner`, Breeds `FiltrarPorSpecies`, MedicalRecords `FiltrarPorPet`) se
conservan **por módulo** porque encapsulan reglas de negocio específicas. Además se extrajo el filtro
inline de `status` de Payments a un Filter propio (`Pagos/FiltrarPorEstado`).

> Nota importante de Pipeline: cuando un Filter se pasa a `through()` como **string de clase**
> (`FiltrarPorX::class`), el contenedor le inyecta el `Request` global (`request()`), que en runtime
> coincide con el del controller — pero para que la Action sea el único dueño de la query con su
> `$request` explícito, **todos** los Filters (dominio y compartidos) se pasan como **instancias**
> (`new FiltrarPorX($request, ...)`). `Illuminate\Pipeline\Pipeline` acepta objetos en `through()`.

---

## Qué se movió

### 1. Filters creados

- `app/Filters/Shared/FiltrarPorBusqueda.php` — genérico, columnas por constructor.
- `app/Filters/Shared/OrdenarPor.php` — genérico, mapa ordenable + default por constructor.
- `app/Filters/Pagos/FiltrarPorEstado.php` — extraído del `when(filled("status"))` inline de Payments.

### 2. Actions de lectura creadas (16)

Cada `List{Modulo}Action` arma la query con el Pipeline nativo (Filters de dominio + compartidos),
aplica `->paginate($request->integer("per_page", 15))` y devuelve un `LengthAwarePaginator`. El
controller **queda delgado**: solo `$paginated = $action->execute($request);` + envelope.

| Módulo | Action | Filtros que usa |
|---|---|---|
| Branches | `Branches/ListBranchesAction` | Busqueda (name/address/city/phone), Orden |
| Species | `Species/ListSpeciesAction` | Busqueda (name), Orden |
| Breeds | `Breeds/ListBreedsAction` | `FiltrarPorSpecies` + Busqueda (name) + Orden |
| Services | `Services/ListServicesAction` | Busqueda (name/category), Orden |
| VaccineTypes | `VaccineTypes/ListVaccineTypesAction` | Busqueda (name), Orden |
| Medicines | `Medicines/ListMedicinesAction` | Busqueda (name), Orden |
| Owners | `Owners/ListOwnersAction` | Busqueda (first/last/email/phone) + Orden + `withCount('pacientes')` |
| Pacientes | `Pacientes/ListPacientesAction` | Busqueda (name), Orden |
| Users | `Users/ListUsersAction` | Busqueda (username/email), Orden |
| VeterinarianSchedules | `VeterinarianSchedules/ListVeterinarianSchedulesAction` | Busqueda (day_of_week), Orden |
| Citas | `Citas/ListCitasAction` | `FiltrarPorVeterinario/Fecha/Estado` + Busqueda + Orden |
| Vacunas | `Vacunas/ListVacunasAction` | Busqueda (vaccination_date), Orden |
| Cirugias | `Cirugias/ListCirugiasAction` | Busqueda (surgery_date), Orden |
| MedicalRecords | `MedicalRecords/ListMedicalRecordsAction` | `FiltrarPorPet` + Busqueda + Orden |
| Invoices | `Facturacion/ListInvoicesAction` | `FiltrarPorEstado/RangoFecha/Owner` + Busqueda + Orden |
| Payments | `Facturacion/ListPaymentsAction` | `Pagos/FiltrarPorEstado` + Busqueda + Orden |

### 3. Controllers refactorizados (16 `Api/Admin/*`)

`index()` ahora inyecta la Action y solo arma el envelope `{success, data, pagination}` (igual que
en Fix #5). Se eliminaron los imports que quedaron sin uso (`Builder`, `Pipeline`, Filters de dominio).

```php
public function index(Request $request, ListOwnersAction $action): JsonResponse
{
    $paginated = $action->execute($request);

    return response()->json([
        "success" => true,
        "data" => OwnerResource::collection($paginated->items()),
        "pagination" => [
            "total" => $paginated->total(),
            "per_page" => $paginated->perPage(),
            "current_page" => $paginated->currentPage(),
            "last_page" => $paginated->lastPage(),
            "has_more" => $paginated->currentPage() < $paginated->lastPage(),
        ],
    ]);
}
```

---

## Auditoría de `app/Http/Controllers/Admin/**` (web)

Revisados los 15 controllers web uno por uno:

- **14 de 15** ya hacían solo `return view(...)` sin lógica de paginación → **sin cambios** (no hay
  nada que mover a una Action).
- **`Admin/MedicalRecordController@index`** era el único con lógica de consulta/paginación real
  (`->when(filled("pet_id"))...->orderByDesc("event_date")->paginate(10)`). Además el blade
  `medical-records/index.blade.php` **nunca usaba `$records`**: el `#table-medical-records` es una
  DataTable server-side que consume el API `api/admin/medical-records`. Se **eliminó la lógica muerta**:
  el index web ahora solo pasa `$pacientes` (para el select de filtro), consistente con los otros 14.
- `Admin/PacienteController`, `OwnerController`, `InvoiceController` y demás conservan sus helpers
  privados de selects/formularios (no son lógica de index).

---

## Confirmación: envelope de respuesta sin cambios

- El envelope de **todos** los `index()` es idéntico al del Fix #5: `{success, data, pagination}`.
- `store/update/destroy/show` y métodos personalizados conservan `{status, message, data, errors}`
  (sin cambios; se verificó que no se tocaron).
- El JS de DataTables **no requirió cambios** (verificado con `node --check` en los 17 archivos):
  sigue consumiendo `success/data/pagination`.

---

## Pruebas realizadas

- `php -l` OK: 16 `Api/Admin/*Controller`, 15 `Admin/*Controller` web, 16 `List*Action`, 3 Filters nuevos.
- `node --check` OK: 17 `public/js/pages/*.js` (JS intacto).
- `view:cache` OK; `route:list api/admin` OK.
- Tinker (Actions con `Request::create` con query params):
  - Breed `species_id=1` → **8** (29 total) → `FiltrarPorSpecies` aplica.
  - Cita `status=pendiente` → **5**; Invoice `status=pagado` → **2**; Payment `status=pagado` → **6**.
  - Branch: `per_page=2` → 2 items / total 3; `search=Lima` → 3; `sort_by=1&sort_dir=desc` →
    primer registro "Sede San Isidro" (orden correcto).
  - Todas las 16 Actions devuelven `LengthAwarePaginator` con `total` sano.
- HTTP real (`app()->handle(Request::create('/api/admin/branches'))` y `/api/admin/owners`):
  **200** con `{success, true, data: [...]}`.
- Test suite: 20 fallos preexistentes de `Auth`/`Profile` (scaffold Laravel, `QueryException` por DB
  no migrada en test) — **ninguno** relacionado con los controllers refactorizados.

---

## Archivos del cambio

- Creados: `app/Filters/Shared/{FiltrarPorBusqueda,OrdenarPor}.php`, `app/Filters/Pagos/FiltrarPorEstado.php`,
  16 `app/Actions/*/List*Action.php`.
- Modificados: 16 `app/Http/Controllers/Api/Admin/*Controller.php` (index delgado), 1
  `app/Http/Controllers/Admin/MedicalRecordController.php` (index web sin lógica muerta).
