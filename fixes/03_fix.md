## Fix #3 — Pipeline de filtros ausente + reglas de negocio incompletas (solo API)

**Tipo:** Fix de arquitectura/consistencia, alcance amplio (varios módulos)

**Prompt:**

Antes de tocar nada, lee en este orden:
1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, Actions, Filters, Resources)

**Alcance estricto:** Solo `app/Http/Controllers/Api/Admin/**`, `app/Actions/**`, `app/Filters/**`, `app/Http/Requests/**`. **No toques nada de Blade, JS, ni rutas de `routes/web.php`.** **No toques el módulo de autenticación** (Breeze, `AuthController`, login/registro/logout, Spatie) bajo ninguna circunstancia.

Este fix tiene dos partes. Haz un audit completo primero (lee todos los Controllers Api/Admin existentes) y anota qué módulos fallan cada regla antes de empezar a corregir.

---

### Parte 1 — Pipeline de filtros ausente en la mayoría de módulos

Según `project-map.md`, el patrón `App\Filters\Pipeline::apply()` con clases `Filtrar*` (usado con `Pipeline::send()->through()->thenReturn()`) **solo está implementado en `Citas`**. Todos los demás módulos que tengan filtros útiles en su `index()` (por query params) deben seguir el mismo patrón, no condicionales `if` sueltos dentro del controller.

Para cada módulo con filtros razonables, crea sus clases `App\Filters\{Modulo}\Filtrar*` y aplica el pipeline en el `index()`. Ejemplos de filtros esperables por módulo (ajusta según lo que realmente tenga sentido consultar):

- `Pacientes`: por `owner_id`, por `species_id`, por nombre
- `Owners`: por nombre/documento
- `Vacunas`: por `pet_id`, por `vaccine_type_id`, por rango de fecha
- `Cirugías`: por `pet_id`, por `status`, por rango de fecha
- `MedicalRecords`: ya filtra por `pet_id` (según project-map) — migrarlo también al patrón Pipeline si no lo usa
- `Users`: por rol, por `is_active`
- Módulos de catálogo simple (`Species`, `Breeds`, `Services`, `VaccineTypes`, `Medicines`, `Branches`) solo si ya tienen algún filtro real en uso; si no filtran nada, no hace falta forzar un Pipeline vacío

No inventes filtros que no tengan uso real en el frontend actual — revisa qué query params ya envían los JS de `resources/js/pages/*` antes de decidir cuáles formalizar en Filters.

### Parte 2 — Pagination(15) consistente (confirmar/completar)

Confirma que **todos** los `index()` de Api/Admin usan `->paginate(15)` (no `->get()`). Si algún módulo quedó sin este fix aplicado, corrígelo ahora con el mismo formato de respuesta (bloque `meta` con `current_page`, `last_page`, `per_page`, `total`) ya usado en los módulos que sí lo tienen.

### Parte 3 — Reglas de negocio incompletas en Actions

`CreateCitaAction` ya valida que el veterinario no tenga otra cita en conflicto de horario (cruce contra `veterinarian_schedules` + otras citas del mismo vet/fecha/hora), según `project-map.md`. Esa es la referencia de "regla de negocio real" — no es una simple validación de campo, es una regla que necesita consultar otros registros, y por eso vive en la Action, no en el Form Request.

Audita si existen reglas de negocio equivalentes que **faltan** en otros módulos con el mismo tipo de conflicto potencial (agenda/horario/recurso compartido):

- `CreateCirugiaAction` / `UpdateCirugiaAction`: si una cirugía tiene `surgery_date` y `veterinarian_id`, ¿se valida que el veterinario no tenga otra cirugía o cita en conflicto en ese mismo rango de tiempo? Si no, agrégalo siguiendo el mismo criterio que `Citas`.
- `CreateVacunaAction`: si la vacuna se aplica en una fecha/hora específica ligada a un veterinario, evalúa si aplica la misma regla o si por su naturaleza no la necesita (justifica la decisión en el fix doc, no la agregues si no tiene sentido real).
- Cualquier otro Action que hoy solo haga `Modelo::create($data)` sin ninguna validación cruzada, cuando el dominio sí lo amerita (ejemplo: no permitir eliminar un `owner` con mascotas activas — confirma que esto ya existe según project-map, si no, agrégalo).

No agregues reglas de negocio inventadas que no tengan sentido para una clínica veterinaria real — si un módulo no necesita validación cruzada, dilo explícitamente en el documento de fix en vez de forzar código innecesario.

---

**Al terminar:**

Crea `/fixes/2026-08-06-pipeline-filtros-y-reglas-negocio.md` documentando:
- Tabla: módulo → tenía Pipeline (sí/no) → filtros agregados
- Tabla: módulo → tenía paginate(15) (sí/no) → corregido
- Tabla: módulo → regla de negocio evaluada → resultado (se agregó / ya existía / no aplica y por qué)
- Fecha del fix

Actualiza la tabla "Fixes aplicados" y la tabla "Filters registrados" en `project-map.md` con los nuevos Filters creados.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

No toques Blade, JS, ni el módulo de autenticación. Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
