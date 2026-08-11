# Diseño: Index de Historial Médico con cards y tabs

**Fecha:** 2026-08-11
**Estado:** Aprobado

## Problema

El index de `admin/medical-records` (`resources/views/admin/medical-records/index.blade.php`) usa una DataTable server-side genérica con auto-búsqueda y "Showing 1 to 10 entries". El usuario quiere un listado visual por **tarjetas (cards)** filtrando por mascota y con **tabs por tipo de evento** (Citas/Vacunas/Cirugías), sin el DataTable.

## Alcance

- Solo UI del index + JS. Backend intacto (el endpoint `GET /api/admin/medical-records` ya devuelve `pet_name`, `veterinarian`, `event_type`, `event_date`, `notes`, `show_url` y soporta `pet_id`).
- El `show.blade.php` **no se toca** (el usuario pidió dejarlo como está).

## Requisitos funcionales

1. **Filtro por mascota:** select de mascotas (`#filtro_pet_id`). Al seleccionar una mascota se cargan todos sus registros vía `GET /api/admin/medical-records?pet_id=X` y se renderizan cards.
2. **Tabs por tipo:** Todos | Citas | Vacunas | Cirugías.
   - Mapeo: `Citas` → `event_type = consulta`; `Vacunas` → `vacuna`; `Cirugías` → `cirugia`; los `otro` solo aparecen en **Todos**.
   - Filtrado en cliente (sin paginación ni server-side). Sin DataTable.
3. **Cards:** grid responsive. Cada card es clickeable al show y muestra:
   - Fecha formateada (`d/m/Y`).
   - Badge de tipo.
   - Veterinario.
   - Notas (máx 3 líneas, `-webkit-line-clamp`).
4. **Empty states:**
   - Sin mascota seleccionada → "Selecciona una mascota para ver su historial".
   - Mascota sin registros o tab sin resultados → "Sin historial de X" / "Sin X".
5. Se elimina la DataTable y su auto-search/"Showing 1 to 10". El botón "Nueva entrada" se mantiene.

## Componentes

- `resources/views/admin/medical-records/index.blade.php`: header, select mascota, tabs, contenedor de cards, contador, empty states.
- `public/js/pages/medical-records.js`: limpiar el bloque de DataTable del index; nueva lógica `cargarHistorial(petId)` + `renderCards(records)` + filtro por tab + `pintarVacio(html)`. Se conservan las funciones del show/create (pintarRecetas, adjuntos, eliminar, etc.).

## Verificación

- `php -l`, `node --check`, `php artisan view:cache`.
- HTTP: render del index (login) con tabs y contenedor; endpoint API con `pet_id` devuelve los registros de esa mascota.