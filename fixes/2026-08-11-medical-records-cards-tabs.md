# Fix: Index de Historial Médico con cards y tabs (2026-08-11)

**Prompt relacionado:** Prompt #22

## Problema

El listado de `admin/medical-records` usaba una DataTable server-side genérica (auto-búsqueda, "Showing 1 to 10 entries") que el usuario consideró fea. Pidió: mantener el filtro por mascota, pero que al seleccionarla cargue su historial en tarjetas con tabs por tipo (Citas/Vacunas/Cirugías), y que el show no se toque.

## Solución

- **`resources/views/admin/medical-records/index.blade.php`**: se eliminó la DataTable; select de mascota que dispara con `change`; bloque con 4 tabs (Todos/Citas/Vacunas/Cirugías) con badges de contador; empty state; estilo `medical-card` hover + `notas-clamp` (3 líneas).
- **`public/js/pages/medical-records.js`**: se eliminó el bloque DataTable del index (`cargarDatos`, `construirDataTable`, `getFiltros`); nueva lógica `MAPA_TIPOS`/`TIPOS_TABS`/`renderTarjeta`/`renderVacio`/`pintarTab`/`cargarHistorial` con filtrado en cliente sobre `GET /api/admin/medical-records?pet_id=X`. El bloque show/create (recetas, adjuntos, eliminar) quedó intacto.

## Mapeo de tabs

| Tab | `event_type` filtrado | Observación |
|-----|----------------------|-------------|
| Todos | (todos) | incluye `otro` |
| Citas | `consulta` | — |
| Vacunas | `vacuna` | — |
| Cirugías | `cirugia` | — |

## Archivos tocados

- `resources/views/admin/medical-records/index.blade.php`
- `public/js/pages/medical-records.js`

## Verificación

- `node --check public/js/pages/medical-records.js` OK
- `php -l` del blade OK
- `php artisan view:cache` OK
- HTTP: index 200 con `#tabsHistorial`, 4 `data-tipo`, `#filtro_pet_id`, `#estadoVacio` y **0** `table-medical-records`
- API `pet_id=12` → 2 registros (vacuna + cirugía) con los campos que consume la card
- Nota OPcache: el servidor siguió sirviendo la vista compilada vieja ~1 min tras `view:cache` (revalidate_freq=180), como en fixes anteriores.

## Fix posterior: las citas agendadas no aparecían (Prompt #22b)

**Problema:** una mascota con citas, cirugías y vacunas mostraba cirugías/vacunas pero no citas. Causa: el tab "Citas" filtraba `event_type=consulta` sobre medical_records, y la mascota tenía citas agendadas (tabla `citas`) sin medical record de consulta.

**Solución:**
- `ListCitasAction` ahora filtra por `pet_id` (reutilizando `FiltrarPorPet`), permitiendo `GET /api/admin/citas?pet_id=X`.
- `cargarHistorial()` une records + citas vía `Promise.all`, normaliza cada uno con `aEventoHistorial()` y ordena por fecha desc.
- `TIPOS_TABS.citas = ["consulta","cita"]` para mostrar citas agendadas y medical records de consulta en el mismo tab; `MAPA_TIPOS` agrega `cita`.

**Verificado:** API citas con `pet_id=12` → cita #41; simulación node → Todos=3 (cirugía/vacuna/cita), Citas=cita, Vacunas=vacuna, Cirugías=cirugía.

**Estado:** verificado.