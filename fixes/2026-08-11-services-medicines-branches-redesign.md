# Rediseño de Servicios, Medicamentos y Sucursales (Prompt #25)

**Fecha:** 2026-08-11
**Módulos:** `/admin/services`, `/admin/medicines`, `/admin/branches`

## Contexto

Los tres módulos usaban DataTable server-side con el search por defecto de DataTables. El usuario pidió:
búsqueda propia con debounce (sin el search de DataTables), mismo formato de tabla del resto del sistema,
formularios con ID autogenerado + campos required, y para Sucursales el create **en un tab** dentro del índice
(patrón de vaccine-types).

## Cambios

### Backend

Sin cambios. Las Actions ya soportaban `search`:

- `app/Actions/Services/ListServicesAction.php` → `name`, `category`
- `app/Actions/Medicines/ListMedicinesAction.php` → `name`
- `app/Actions/Branches/ListBranchesAction.php` → `name`, `address`, `city`, `phone`

### Vistas

- `resources/views/admin/services/index.blade.php`: form `#formFiltrosServices` con `#busquedaServices` (input-group + `ri-search-line`) + botones Filtrar/Limpiar.
- `resources/views/admin/medicines/index.blade.php`: form `#formFiltrosMedicines` con `#busquedaMedicines`.
- `resources/views/admin/branches/index.blade.php`: reescrito con tabs `nav-tabs-custom` — Listado y Nueva Sucursal. El botón "Nueva Sucursal" cambia al tab. Form en tab: ID "Autogenerado" disabled, campos `required`, input-groups con íconos.
- `resources/views/admin/services/{create,edit}.blade.php` y `medicines/{create,edit}.blade.php`: adaptativos (`col-12` + `row justify-content-center`), header con ícono, campo ID, input-groups con íconos `ri-*`, `required`.
- `resources/views/admin/branches/edit.blade.php`: rediseñado igual que el tab de creación.

### JS

- `public/js/pages/services.js`, `medicines.js`, `branches.js`:
  - `terminoBusqueda` + timer de debounce (350ms) en `#busquedaServices`/`#busquedaMedicines`/`#busquedaBranches`.
  - DataTable con `searching: false`, `lengthChange: false`, `info: false`.
  - Eliminada la lectura de `data.search.value` (ya no se envía el search de DataTables).
  - Submit del form de filtros → `dataTable.ajax.reload()`; "Limpiar" → reset + `limpiarBuscador()` + reload.
- `public/js/pages/branches.js`:
  - `dataTable` movida al scope del IIFE (para poder recargar desde el submit del tab).
  - `activarTabNuevaBranch()` / `activarTabListado()` con `new bootstrap.Tab` (patrón vaccine-types).
  - Submit de `#formCrearBranch`: Swal + `volverAlListado()` (reset form + tab Listado) + `dataTable.ajax.reload()` (ya no redirige).

## Verificación

- `node --check` de los 3 JS: OK.
- `php -l` de controllers: OK.
- `view:clear` + `view:cache`: OK.
- HTTP con sesión (`carlos.torres@veterinaria.com`): índices y create/edit de los 3 módulos devuelven 200.
- Búsqueda via API:
  - `GET /api/admin/services?search=cirug` → 2 (Cirugía general, Esterilización).
  - `GET /api/admin/branches?search=lima` → 3 (La Molina, Miraflores, San Isidro).
  - `GET /api/admin/medicines?search=a` → 5 (no existe medicamento con "penic").
- La API usa prefijo `/api` (el helper `ajax` de `public/js/config/ajax.js` antepone `baseURL = "/api"`).
