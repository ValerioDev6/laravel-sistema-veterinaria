# Fix #7 — Citas duplicadas en el mismo horario (diagnóstico + fix real)

**Tipo:** Fix de bug — diagnóstico primero, causa confirmada antes de corregir

**Fecha:** 2026-08-10

**Prompt de referencia:** `fixes/07_fix.md`

---

## Resultado del diagnóstico: NO se duplica en la base de datos

Se reprodujo el caso (registrar una cita para el mismo veterinario, misma fecha, misma hora donde ya existe otra cita activa) y se revisó directo en la tabla `citas`. **Solo quedó 1 registro**: el backend rechaza correctamente con HTTP 422 en ambas capas de validación:

- **`StoreCitaRequest` / `UpdateCitaRequest`** — regla `validarHorario` (con y sin exclusión de la propia cita en update).
- **`ValidarDisponibilidadCita::execute()`** — llamado dentro de la transacción de `CreateCitaAction` / `UpdateCitaAction`, con `whereRaw('TIME(appointment_time) = ?')` y mensajes "La hora seleccionada está fuera del horario de atención del veterinario para ese día." / "El veterinario ya tiene una cita a esa hora."

**Causa raíz exacta: bug de UI.** El 422 llegaba correctamente al frontend, pero la capa AJAX compartida (`public/js/config/ajax.js`) lo rechazaba con `reject(error)` **sin mostrar ningún mensaje**, y el `.catch` de `citas.js` solo llamaba `pintarErroresValidacion()`, que pinta el error inline en el campo. El campo `appointment_time` es `<input type="hidden">` sin `.mb-3`/`.invalid-feedback` visible → **el error quedaba invisible para el usuario**, que creía que el sistema "dejaba pasar" el registro.

### Evidencia del diagnóstico (log temporal)

Se agregó temporalmente en `ValidarDisponibilidadCita::execute()`:

```php
\Log::info('Validando disponibilidad', ['date' => $date, 'time' => $time, 'vetId' => $vetId]);
```

- Reproducción HTTP real (`POST /api/admin/citas` con par ocupado vet=2 / 2026-08-24 / 09:00) → backend respondió 422 `"El veterinario ya tiene una cita a esa hora."` → **no se insertó duplicado**.
- Tinker directo: `ValidarDisponibilidadCita::execute()` lanza `ValidationException` para el par ocupado, no lanza para hora libre, y con `ignorarCitaId=37` permite el mismo horario (update).
- El log quedó vacío en el POST duplicado real porque el `FormRequest` bloquea antes de llegar a la Action; el log sí aparecía en los casos donde el Request dejaba pasar pero la validación de la Action rechazaba (horario fuera de rango). Confirmado que el conflicto por hora ocupada siempre pasa por el Request.

### Falso positivo aclarado

La cita 39 (creada en pruebas de la sesión anterior, prompt #17b) parecía "el bug", pero `09:00` de ese día estaba libre en ese momento porque la cita 37 está a las `07:00:00` (dato de pruebas previas, no del seeder). No era un fallo de validación: era una cita real creada por tests. Estado final de BD: citas 1–8 (seed) + 39, cita 37 a las `07:00:00`.

---

## Qué se corrigió

**`public/js/config/ajax.js`** — la capa AJAX compartida es ahora el **single source of truth** para el manejo de errores 422 (lo que pide el fix doc: consistente en todos los módulos, no solo Citas):

1. Todo `422` con `errors` muestra **siempre** un SweetAlert2 con el mensaje real del backend:
   - `text` = `response?.message || Object.values(response?.errors || {}).flat()[0] || fallback`.
   - `title` según el método: `DELETE` → "No se pudo eliminar"; resto → "No se pudo guardar".
   - Y luego `reject(error)` para que la página siga pintando el inline (`is-invalid`) si lo desea. Sin doble popup: el Swal es único y el inline es complementario.
2. Los 11 handlers de `.catch((error) => { if (error.status === 422) { Swal.fire(...) } })` redundantes de los **deletes** se eliminaron (el Swal global ya los cubre con el mismo mensaje: en los deletes el `message` del `ValidationException` es el mismo que `errors.*[0]`):
   - `breeds.js`, `vaccine-types.js`, `usuarios.js`, `vacunas.js`, `citas.js`, `cirugias.js`, `medicines.js`, `pacientes.js`, `services.js`, `species.js`, `owners.js`.

Los `.catch` de los **formularios de guardar** conservan `pintarErroresValidacion()` (inline) y ahora reciben también el Swal global → el error siempre es visible.

## A qué módulos se extendió el fix (verificados)

La capa AJAX es compartida por **todos** los módulos del admin, así que el fix aplica de forma uniforme a:

- Citas (módulo reportado)
- Vacunas, vaccine-types, cirugías, pacientes, owners, services, medicines
- Usuarios, breeds, species
- Branches, veterinarian-schedules (sus deletes ya tenían `.catch(() => {})`; ahora también muestran el Swal del 422)
- Medical-records e invoices **no** pasan por esta capa (usan `$.ajax` propio con `xhr.responseJSON`); se dejaron intactos porque ya muestran sus mensajes correctamente.

## Lo que NO se tocó (por diagnóstico)

- **Paso 2A (normalización de formato de hora en backend) NO aplicó**: el diagnóstico confirmó que `whereRaw('TIME(appointment_time) = ?', [$time])` ya rechaza el duplicado (el formato `HH:mm` del form vs `TIME` de la columna ya matchea en este flujo). No se normalizó nada en backend porque no había bug de backend confirmado, según el alcance del fix (#07: corregir solo la causa real confirmada).
- El log temporal de diagnóstico fue **quitado** antes de cerrar.

## Estado

- `node --check` OK en `ajax.js` y en los 11 `pages/*.js` editados.
- `php -l` OK en `ValidarDisponibilidadCita.php` (log temporal removido).
- Verificación visual del Swal en navegador: pendiente de confirmación manual por el usuario (no se levantó servidor en esta sesión; el flujo 422 → Swal queda garantizado por código en la capa compartida).
