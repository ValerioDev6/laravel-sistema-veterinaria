# Formato de tabla unificado: Horarios, Personal e Invoices (Prompt #26)

**Fecha:** 2026-08-11
**Módulos:** `/admin/veterinarian-schedules`, `/admin/usuarios`, `/admin/invoices` (+ tab Pagos y show)

## Contexto

Los tres módulos usaban el search por defecto de DataTables. El usuario pidió el mismo formato de tabla
del resto del sistema: búsqueda propia con debounce, sin caja de DataTables. En Invoices aplica también al
tab de Pagos y a la tabla de pagos del show.

## Cambios

### Vistas

- `resources/views/admin/veterinarian-schedules/index.blade.php`: form `#formFiltrosSchedules` con `#busquedaSchedules`
  (input-group + `ri-search-line`) + botones Filtrar/Limpiar.
- `resources/views/admin/usuarios/index.blade.php`: form `#formFiltrosUsuarios` con `#busquedaUsuarios`.
- `resources/views/admin/facturacion/invoices/index.blade.php`: `#busquedaInvoices` (tab Facturas) y `#busquedaPagos`
  (tab Pagos), integradas al form de filtros existente.
- `resources/views/admin/facturacion/invoices/show.blade.php`: la tabla de pagos pasa de `table table-sm` a
  `table table-borderless dt-responsive nowrap` (mismo formato visual del resto).

### JS

- `public/js/pages/veterinarian-schedules.js`, `usuarios.js`, `invoices.js`:
  - `terminoBusqueda` + timer de debounce (350ms) en `#busquedaSchedules` / `#busquedaUsuarios` /
    `#busquedaInvoices` / `#busquedaPagos`.
  - DataTable con `searching: false`, `lengthChange: false`, `info: false`.
  - Eliminado el uso de `data.search.value`; se envía `search` solo cuando hay término.
- `public/js/pages/invoices.js`:
  - Métricas separadas: `terminoBusquedaInvoices` y `terminoBusquedaPagos`.
  - `getFiltros()` ignora el campo `search` para que el `serializeArray` no pise el término debounceado.
  - Eliminada `construirDataTable()` — función muerta que duplicaba la inicialización de ambas tablas.

### Nota sobre schedules

El campo `day_of_week` se guarda como entero (1=Lunes … 6=Sábado). El search filtra por ese entero, por eso el
placeholder dice "Día (1=Lunes, 2=Martes…)".

## Verificación

- `node --check` de los 3 JS: OK.
- `view:clear` + `view:cache`: OK.
- HTTP con sesión (`carlos.torres@veterinaria.com`): los 3 índices y el show devuelven 200.
- Búsqueda via API (`/api/admin/*`):
  - `users?search=carlos` → 1.
  - `veterinarian-schedules?search=1` → 6 (Lunes).
  - `payments?search=tarjeta` → 2.
  - `invoices?search=pagado` → 2.