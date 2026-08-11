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

## Fix posterior: error 500 en el create de Historial (columna `fecha` inexistente)

**Problema reportado:** el usuario creó una entrada manual (Entrada #11, Chent) que salió **sin signos vitales ni prescripciones**, mientras la del seeder (Entrada #6, Copito) sí los tenía. Preguntaba dónde/cómo se registran vitales y recetas.

**Causa raíz:** el `create` del módulo estaba **roto con error 500** — `MedicalRecordController@create` ordenaba las citas por `Cita::orderByDesc("fecha")` y `appointment_date` (columna real de la tabla `citas` no existe; es `appointment_date`/`appointment_time`), lanzando `SQLSTATE[42S22] Unknown column 'fecha'`. El formulario de signos vitales y prescripciones **sí existe** en `create.blade.php` (secciones "Signos vitales (opcional)" y "Prescripciones (opcional)") y el JS ya los enviaba (`vital_signs[]`, `serializarRecetas()`) — pero con el create caído no se podía llegar a usarlos.

**Solución:**
- `app/Http/Controllers/Admin/MedicalRecordController.php`: `orderByDesc("fecha")` → `orderByDesc("appointment_date")` + `with("paciente")`.
- `create.blade.php`: la opción de cita usaba `$cita->fecha`/`$cita->hora` → ahora `$cita->appointment_date?->format('Y-m-d')` / `$cita->appointment_time?->format('H:i')`.

**Verificado:** create 200 con las secciones de vitales/recetas; POST real con vitales (15.5 kg, 38.2 °C, 90 lpm) + receta (Prednisolona, 1 cda c/8h, 5 días) → 201 y el show muestra todo; registro de prueba eliminado (BD restaurada a 10 records).

**Estado:** verificado.