# Servicios, Medicamentos y Sucursales — Rediseño de UI (Servicios, Medicamentos y Sucursales)

## Objetivo

Mejorar la UI de los módulos Servicios, Medicamentos y Sucursales: búsqueda propia con debounce (sin el search por defecto de DataTables), mantener el formato de tabla del resto del sistema, y rediseñar los formularios con ID autogenerado y campos requeridos.

## Alcance

- **Servicios** (`/admin/services`): búsqueda propia + rediseño de create/edit (vistas separadas).
- **Medicamentos** (`/admin/medicines`): búsqueda propia + rediseño de create/edit (vistas separadas).
- **Sucursales** (`/admin/branches`): búsqueda propia + el create **en un tab** dentro del índice (patrón vaccine-types). Editar conserva vista separada.

## Decisiones con el usuario

1. Sucursales: create en tab dentro del índice. El botón "Nueva Sucursal" cambia al tab (no redirige).
2. Servicios/Medicamentos: siguen con vistas separadas create/edit, solo rediseñadas con ID "Autogenerado" + required + íconos.

## Backend

Las Actions ya soportan `search` vía `App\Filters\Shared\FiltrarPorBusqueda`:

- `ListServicesAction` → campos `name`, `category`
- `ListMedicinesAction` → campo `name`
- `ListBranchesAction` → campos `name`, `address`, `city`, `phone`

No se requieren cambios de backend.

## Índices (3 módulos)

Patrón de `admin/vacunas/index.blade.php`:

- Form `row g-3` con caja de búsqueda `input-group` + `ri-search-line`, `type="search"`, debounce 350ms → `dataTable.ajax.reload()`.
- DataTable: `searching: false`, `lengthChange: false`, `info: false`. Se elimina la captura de `data.search.value`.
- Columnas sin cambios.

## Sucursales con tabs

Patrón de `admin/vaccine-types/index.blade.php`:

- Tabs `nav-tabs-custom`: Listado y Nueva Sucursal.
- Botón "Nueva Sucursal" cambia al tab de creación.
- Formulario en tab: ID "Autogenerado" (disabled), Nombre/Dirección/Ciudad/Teléfono `required`, input-groups con íconos.
- Tras guardar: Swal + recargar DataTable + volver al tab Listado + reset form.
- La vista `branches/create.blade.php` queda sin uso directo (se conserva).

## Rediseño de formularios

Adaptativos (`col-12` + `row justify-content-center`), card header con ícono, input-groups con íconos `ri-*`, campo ID "Autogenerado" disabled, `required` en campos obligatorios.

- **Servicios**: Nombre*, Categoría*, Precio base*, Duración (min)*, Descripción (opcional).
- **Medicamentos**: Nombre*, Stock*, Costo Unitario*.
- **Sucursales (tab y edit)**: Nombre*, Dirección*, Ciudad*, Teléfono*.

## JS

- `services.js`, `medicines.js`, `branches.js`: variable `terminoBusqueda` + timer + handler `input` con debounce; DataTable sin `data.search.value`, usando `search: terminoBusqueda`.
- `branches.js`: al guardar en tab → recargar tabla + `bootstrap.Tab` a Listado + reset del form (reemplaza `window.location.href`).
- Submit de create/edit de Services/Medicines sin cambios (ya funciona con `pintarErroresValidacion`).

## Verificación

- `php -l` sobre archivos PHP tocados.
- `node --check` sobre los 3 JS.
- `view:cache` / `view:clear` para soltar OPcache.
- Pruebas HTTP: login + GET de los índices (200, tab abierto correcto), search en API filtra.