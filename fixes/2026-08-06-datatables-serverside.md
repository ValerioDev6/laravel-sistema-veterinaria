# Fix #4 — Migración a DataTables server-side real (contrato oficial)

**Tipo:** Fix de arquitectura, alcance amplio (Blade + JS + API, casi todos los módulos)

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/04_fix.md`

---

## Causa raíz exacta del quiebre

Los listados del admin usaban DataTable en modo **client-side** (`serverSide` ausente o mal configurado), que espera recibir todo el dataset en una sola respuesta. Al introducirse la paginación del backend (15 por página), DataTables interpretaba que esos 15 registros eran el total y no podía navegar más páginas.

Antes de este fix la respuesta ya era server-side pero con el **contrato propio `{data, meta}`** (helper `DataTables::paginate`) y **más un segundo problema**: el `ajax.url` de cada DataTable apuntaba a la **ruta web** `/admin/{modulo}` (que devuelve HTML Blade), no a la **ruta API** `/api/admin/{modulo}`. Además quedaban llamadas crud con el prefijo **inválido `/admin-api/...`** (no existe tal ruta). Con el contrato `{data, meta}` y el `dataSrc` mapeando `res.records*`, DataTables en modo `serverSide` llegaba a interpretar `recordsTotal`, pero nunca se hizo con el contrato oficial de DataTables.

## Decisión de fix

Migrar a **DataTables server-side real** usando el **contrato oficial de DataTables** en los `index()` que alimentan listados, en lugar de revertir la paginación o mantener un enveloper personalizado. Esto escala correctamente (no se trae el dataset completo) y es el formato nativo que consume DataTables.

---

## Parte 1 — Backend: contrato oficial en `index()`

### Helper nuevo: `app/Filters/DataTables.php` → `server()`

Se añadió `DataTables::server($query, $request, $resourceClass, $options, $defaultLength = 15)` que responde:

```json
{
  "draw": 1,
  "recordsTotal": 120,
  "recordsFiltered": 45,
  "data": [ ... ]
}
```

- Lee `draw` (se devuelve tal cual), `start`, `length` (default 15), `search[value]` y `order[0][column]`/`order[0][dir]`.
- `recordsTotal`: total **sin** filtros (count sobre el query base).
- Búsqueda global: si `search[value]` no está vacío, aplica `orWhere(col, like, %value%)` sobre las columnas `'searchable'` configuradas de cada módulo.
- `recordsFiltered`: total **después** de búsqueda/filtros.
- Ordenamiento: mapea `order[0][column]` (índice de columna de DataTables) contra el array `'orderable'` (índice → columna de BD); si no hay match, usa el default del módulo.
- Corte: `skip($start)->take($length)`.
- Serializa **solo la página actual** con `$resourceClass::collection($rows)`.

Opciones por módulo: `searchable` (columnas de BD para la búsqueda global), `orderable` (índice de columna DataTables → columna de BD), `default` (índice columna + dirección por omisión).

**Todos los `index()` de los 16 `Api/Admin/*Controller`** se reescriben para usar `DataTables::server`, manteniendo su `with()`/`withCount()` relations y los Filters del Pipeline (Breeds→`FiltrarPorSpecies`, Citas→`FiltrarPorVeterinario/Fecha/Estado`, MedicalRecords→`FiltrarPorPet`, Invoice→`FiltrarPorEstado/RangoFecha/Owner`).

**Envelope unificado intacto en los demás métodos:** `store`, `update`, `destroy`, `show` (y personalizados como `cambiarEstado`, `anular`, `toggleStatus`, `toggle`) **conservan** `{status, message, data, errors}` sin cambios. El cambio de contrato aplica **solo** al `index()` que DataTables consume.

## Parte 2 — Frontend: DataTables server-side real

En cada `public/js/pages/{modulo}.js`:

1. **`ajax.url` corregido** a la API: `/api/admin/{modulo}` (antes `/admin/{modulo}`, que era la ruta Blade que devuelve HTML).
2. **`serverSide: true, processing: true, pageLength: 15`** (patrón ya presente, se unifica).
3. **`dataSrc`** ahora solo `return res.data` — DataTables lee `draw`/`recordsTotal`/`recordsFiltered` del top-level de la respuesta oficial. Se eliminó el mapeo obsoleto `res.recordsTotal = res.meta?.total`.
4. Las transformaciones de presentación (`renderAcciones`, `renderEstado`, badges, montos `$`/`S/`, labels, `foto`) se mantienen en las funciones `render*` / `dataSrc` que mapean columnas.

Módulos migrados (todos los listados): Branches, Species, Breeds, Services, VaccineTypes, Medicines, Owners, Pacientes, VeterinarianSchedules, Citas, Vacunas, Cirugías, MedicalRecords, Usuarios, Invoices (facturas + pagos).

### Corrección adicional de URLs crud inválidas

`medical-records.js` e `invoices.js` tenían llamadas crud con prefijo **`/admin-api/...`** (inexistente). Se reemplazaron por `/api/admin/...` (ruta Registrada):
- `medical-records/adjuntos`, `adjuntos/{id}`, `medical-records/{id}` y el POST de creación en `medical-records.js`.
- `invoices`, `invoices/{id}/payments`, `payments/{id}/anular`, `invoices/{id}/anular` en `invoices.js`.

En este fix esas llamadas crud **ya respetan el envelope unificado** (el API devuelve `{status,message,...}`) y quedan en la URL correcta.

---

## Módulos migrados a server-side (todos iguales)

Branches, Species, Breeds, Services, VaccineTypes, Medicines, Owners, Pacientes, Users, VeterinarianSchedules, Citas, Vacunas, Cirugías, MedicalRecords, Invoices (facturas + pagos). Todos usan el mismo patrón `DataTables::server` (back) + `serverSide`/`dataSrc → res.data` (front).

---

## Confirmación del envelope unificado

`grep '"status" => true'` confirma que `store`, `update`, `destroy`, `show` y métodos personalizados en los 16 controllers conservan su envelope `{status, message, data, errors}`. **Ninguno** de esos métodos fue tocado; solo cambió `index()`.

---

## Pruebas realizadas

- `php -l` OK en `app/Filters/DataTables.php` y los 16 controllers.
- `node --check` OK en los 16 `public/js/pages/*.js` tocados.
- Los 16 `index()` devuelven el contrato oficial `{draw, recordsTotal, recordsFiltered, data}` (verificado vía tinker sobre cada control).
- Navegación: `start=2&length=2` en Branches → `data_count=1` (paginación server-side correcta).
- Ordenamiento: columna 1 (nombre) `desc` en Branches → orden correcto.
- Búsqueda global: `search=value="pagado"` en Invoices → `recordsFiltered=2/recordsTotal=8`; `search=value="2026"` en Citas → sin error.
- Filtros Pipeline: breeds por `species_id`, medical-records por `pet_id` siguen aplicándose.

### Cómo probar en el navegador (instrucciones)

1. Arranca el backend (`php artisan serve`) y entra como admin (`/admin/branches` u otro listado).
2. Navega entre páginas (paginación inferior) — debe mostrar `records` por página y permitir saltar de página.
3. Escribe en el buscador global de una columna (ej. Nombre de sucursal) — la tabla debe refrescar mostrando solo lo filtrado y el contador de "registros" debe reflejar el total filtrado.
4. Haz clic en los encabezados ordenables de las columnas que mapeamos (`Name`, `City`) — el orden debe cambiar (asc/desc) recargando desde el server.
5. Verifica que crear/editar siguen mostrando los toast y redirects normales (envelope intacto).

---

## Motores de cambio

- `app/Filters/DataTables.php` — nuevo `DataTables::server()` (helper server-side oficial).
- 16 `app/Http/Controllers/Api/Admin/*Controller.php` — `index()` reescritos a `DataTables::server`.
- 15 `public/js/pages/*.js` — `ajax.url` corregido a `/api/admin/{x}`, `dataSrc→res.data`, limpieza de `/admin-api/...`.

## Estado de sintaxis

- `php -l` OK (helper + 16 controllers).
- `node --check` OK (15 JS de listados + citas-calendar/paciente-ficha sin cambios).
- Balance correcto del contrato en los 16 endpoints.