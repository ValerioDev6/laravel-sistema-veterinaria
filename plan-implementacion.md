# Plan de Implementación — Sistema Veterinaria

> **Fuente de verdad funcional:** `proyecto-veterinaria.md`
> **Fase 1 (Auth + RBAC + Layout Velzon):** YA IMPLEMENTADA — no se toca.
> **Fase 9 (Reportes):** Fuera de alcance por ahora.

---

## Convenciones de este plan

Cada módulo sigue el **orden de construcción obligatorio**:

1. **Form Request** (`Store*Request` + `Update*Request`, separados)
2. **Action** (lógica de negocio + `DB::transaction()` si escribe en 2+ tablas)
3. **Filters** (Pipeline), solo si el listado necesita filtros
4. **Controller Api/Admin/*** + ruta en `routes/api.php`
5. **Resource** (`app/Http/Resources/`)
6. **Controller Admin/*** + ruta en `routes/web.php`
7. **Vista Blade** (reutilizar HTML Velzon existente) + **JS** en `resources/js/pages/`

Notación:
- `[TX]` = requiere `DB::transaction()`
- `[F]` = requiere Filters (Pipeline)
- `[S]` = requiere Seeder manual

---

## Fase 0 — Infraestructura transversal

Antes de cualquier módulo de negocio, crear las piezas compartidas que todas las fases consumen.

### 0.1 Capa AJAX (`public/js/config/ajax.js`)
- Verificar si ya existe en `public/js/config/ajax.js`; si no, crear el archivo con la implementación definida en la spec (`window.ajax` con `XMLHttpRequest`, CSRF automático, interceptor SweetAlert2)

### 0.2 Helpers JS de formulario
- Crear `public/js/helpers/form-helpers.js`: funciones reutilizables `pintarErroresValidacion(errors, formId)`, `limpiarErroresValidacion(formId)`, `serializarFormulario(formId)`
- Cargar en el layout base después de `ajax.js`

### 0.3 Verificar/completar modelos Eloquent
- Revisar los 20 modelos ya existentes
- Completar `$fillable`, `$casts`, relaciones faltantes — sin reescribir ni regenerar
- No tocar `User.php` salvo agregar relaciones de negocio faltantes (`branch()`, `schedules()`, `citas()`, etc.)

### 0.4 Layout base — cargar dependencias faltantes
- Verificar que `app.blade.php` cargue jQuery, DataTables CSS/JS, SweetAlert2 CSS/JS
- Si faltan, agregarlos vía CDN según la spec
- Agregar los `@stack('styles')` y `@stack('scripts')` si no existen

---

## Fase 2 — Sedes y personal

### Módulo 2.1: Branches (Sucursales) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 2.1.1 | Form Request | `app/Http/Requests/Branches/StoreBranchRequest.php`, `UpdateBranchRequest.php` |
| 2.1.2 | Actions | `app/Actions/Branches/CreateBranchAction.php`, `UpdateBranchAction.php`, `DeleteBranchAction.php` |
| 2.1.3 | Controller Api | `app/Http/Controllers/Api/Admin/BranchController.php` |
| 2.1.4 | Rutas API | `routes/api.php` → `admin/branches` (index, store, show, update, destroy) |
| 2.1.5 | Resource | `app/Http/Resources/BranchResource.php` |
| 2.1.6 | Controller Admin | `app/Http/Controllers/Admin/BranchController.php` (index, create, edit) |
| 2.1.7 | Rutas Web | `routes/web.php` → `admin/branches` (index, create, edit) |
| 2.1.8 | Vista index | `resources/views/admin/branches/index.blade.php` + DataTable |
| 2.1.9 | Vista crear | `resources/views/admin/branches/create.blade.php` |
| 2.1.10 | Vista editar | `resources/views/admin/branches/edit.blade.php` |
| 2.1.11 | JS de página | `public/js/pages/branches.js` |
| 2.1.12 | Seeder | `database/seeders/BranchSeeder.php` (datos manuales realistas) |

### Módulo 2.2: Users (Personal) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 2.2.1 | Form Request | `app/Http/Requests/Users/StoreUserRequest.php`, `UpdateUserRequest.php` |
| 2.2.2 | Actions | `app/Actions/Users/CreateUserAction.php`, `UpdateUserAction.php`, `ToggleUserStatusAction.php` |
| 2.2.3 | Controller Api | `app/Http/Controllers/Api/Admin/UserController.php` (store, update, toggleStatus, destroy) |
| 2.2.4 | Rutas API | `routes/api.php` → `admin/users` (index, store, show, update, destroy, toggleStatus) |
| 2.2.5 | Resource | `app/Http/Resources/UserResource.php` |
| 2.2.6 | Controller Admin | `app/Http/Controllers/Admin/UserController.php` (index, create, edit) |
| 2.2.7 | Rutas Web | `routes/web.php` → `admin/users` (index, create, edit) |
| 2.2.8 | Vista index | `resources/views/admin/usuarios/index.blade.php` + DataTable |
| 2.2.9 | Vista crear | `resources/views/admin/usuarios/create.blade.php` (select de rol existente, select de branch) |
| 2.2.10 | Vista editar | `resources/views/admin/usuarios/edit.blade.php` |
| 2.2.11 | JS de página | `public/js/pages/usuarios.js` |
| 2.2.12 | Seeder | `database/seeders/UserSeeder.php` (personal realista: 2-3 veterinarios, 1-2 recepcionistas) |

> **Nota sobre Users:** El `Store` asigna un rol ya existente vía `$user->assignRole($role)` (Spatie). No se crean roles ni permisos nuevos. El avatar se sube a Cloudinary.

---

## Fase 3 — Catálogos base

### Módulo 3.1: Species (Especies) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 3.1.1 | Form Request | `app/Http/Requests/Species/StoreSpeciesRequest.php`, `UpdateSpeciesRequest.php` |
| 3.1.2 | Actions | `app/Actions/Species/CreateSpeciesAction.php`, `UpdateSpeciesAction.php`, `DeleteSpeciesAction.php` |
| 3.1.3 | Controller Api | `app/Http/Controllers/Api/Admin/SpeciesController.php` |
| 3.1.4 | Rutas API | `routes/api.php` → `admin/species` |
| 3.1.5 | Resource | `app/Http/Resources/SpeciesResource.php` |
| 3.1.6 | Controller Admin | `app/Http/Controllers/Admin/SpeciesController.php` |
| 3.1.7 | Rutas Web | `routes/web.php` → `admin/species` |
| 3.1.8 | Vista index | `resources/views/admin/species/index.blade.php` + DataTable |
| 3.1.9 | Vista crear | `resources/views/admin/species/create.blade.php` |
| 3.1.10 | Vista editar | `resources/views/admin/species/edit.blade.php` |
| 3.1.11 | JS de página | `public/js/pages/species.js` |
| 3.1.12 | Seeder | `database/seeders/SpeciesSeeder.php` (Perro, Gato, Conejo, Hámster, Ave, Tortuga, etc.) |

### Módulo 3.2: Breeds (Razas) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 3.2.1 | Form Request | `app/Http/Requests/Breeds/StoreBreedRequest.php`, `UpdateBreedRequest.php` |
| 3.2.2 | Actions | `app/Actions/Breeds/CreateBreedAction.php`, `UpdateBreedAction.php`, `DeleteBreedAction.php` |
| 3.2.3 | Controller Api | `app/Http/Controllers/Api/Admin/BreedController.php` |
| 3.2.4 | Rutas API | `routes/api.php` → `admin/breeds` |
| 3.2.5 | Resource | `app/Http/Resources/BreedResource.php` |
| 3.2.6 | Controller Admin | `app/Http/Controllers/Admin/BreedController.php` |
| 3.2.7 | Rutas Web | `routes/web.php` → `admin/breeds` |
| 3.2.8 | Vista index | `resources/views/admin/breeds/index.blade.php` + DataTable (muestra especie asociada) |
| 3.2.9 | Vista crear | `resources/views/admin/breeds/create.blade.php` (select de especie) |
| 3.2.10 | Vista editar | `resources/views/admin/breeds/edit.blade.php` |
| 3.2.11 | JS de página | `public/js/pages/breeds.js` |
| 3.2.12 | Seeder | `database/seeders/BreedSeeder.php` (razas reales por especie: Labrador, Pastor Alemán, Persa, Siamés, etc.) |

### Módulo 3.3: Services (Servicios) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 3.3.1 | Form Request | `app/Http/Requests/Services/StoreServiceRequest.php`, `UpdateServiceRequest.php` |
| 3.3.2 | Actions | `app/Actions/Services/CreateServiceAction.php`, `UpdateServiceAction.php`, `DeleteServiceAction.php` |
| 3.3.3 | Controller Api | `app/Http/Controllers/Api/Admin/ServiceController.php` |
| 3.3.4 | Rutas API | `routes/api.php` → `admin/services` |
| 3.3.5 | Resource | `app/Http/Resources/ServiceResource.php` |
| 3.3.6 | Controller Admin | `app/Http/Controllers/Admin/ServiceController.php` |
| 3.3.7 | Rutas Web | `routes/web.php` → `admin/services` |
| 3.3.8 | Vista index | `resources/views/admin/services/index.blade.php` + DataTable |
| 3.3.9 | Vista crear | `resources/views/admin/services/create.blade.php` (enum `category`, precio base, duración) |
| 3.3.10 | Vista editar | `resources/views/admin/services/edit.blade.php` |
| 3.3.11 | JS de página | `public/js/pages/services.js` |
| 3.3.12 | Seeder | `database/seeders/ServiceSeeder.php` (Consulta general, Vacunación, Esterilización, Baño, etc.) |

### Módulo 3.4: Vaccine Types (Tipos de vacuna) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 3.4.1 | Form Request | `app/Http/Requests/VaccineTypes/StoreVaccineTypeRequest.php`, `UpdateVaccineTypeRequest.php` |
| 3.4.2 | Actions | `app/Actions/VaccineTypes/CreateVaccineTypeAction.php`, `UpdateVaccineTypeAction.php`, `DeleteVaccineTypeAction.php` |
| 3.4.3 | Controller Api | `app/Http/Controllers/Api/Admin/VaccineTypeController.php` |
| 3.4.4 | Rutas API | `routes/api.php` → `admin/vaccine-types` |
| 3.4.5 | Resource | `app/Http/Resources/VaccineTypeResource.php` |
| 3.4.6 | Controller Admin | `app/Http/Controllers/Admin/VaccineTypeController.php` |
| 3.4.7 | Rutas Web | `routes/web.php` → `admin/vaccine-types` |
| 3.4.8 | Vista index | `resources/views/admin/vaccine-types/index.blade.php` + DataTable (muestra especie si aplica) |
| 3.4.9 | Vista crear | `resources/views/admin/vaccine-types/create.blade.php` (select opcional de especie) |
| 3.4.10 | Vista editar | `resources/views/admin/vaccine-types/edit.blade.php` |
| 3.4.11 | JS de página | `public/js/pages/vaccine-types.js` |
| 3.4.12 | Seeder | `database/seeders/VaccineTypeSeeder.php` (Séxtuple canina, Triple felina, Rabia, etc.) |

### Módulo 3.5: Medicines (Medicamentos) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 3.5.1 | Form Request | `app/Http/Requests/Medicines/StoreMedicineRequest.php`, `UpdateMedicineRequest.php` |
| 3.5.2 | Actions | `app/Actions/Medicines/CreateMedicineAction.php`, `UpdateMedicineAction.php`, `DeleteMedicineAction.php` |
| 3.5.3 | Controller Api | `app/Http/Controllers/Api/Admin/MedicineController.php` |
| 3.5.4 | Rutas API | `routes/api.php` → `admin/medicines` |
| 3.5.5 | Resource | `app/Http/Resources/MedicineResource.php` |
| 3.5.6 | Controller Admin | `app/Http/Controllers/Admin/MedicineController.php` |
| 3.5.7 | Rutas Web | `routes/web.php` → `admin/medicines` |
| 3.5.8 | Vista index | `resources/views/admin/medicines/index.blade.php` + DataTable (stock, costo) |
| 3.5.9 | Vista crear | `resources/views/admin/medicines/create.blade.php` |
| 3.5.10 | Vista editar | `resources/views/admin/medicines/edit.blade.php` |
| 3.5.11 | JS de página | `public/js/pages/medicines.js` |
| 3.5.12 | Seeder | `database/seeders/MedicineSeeder.php` (Amoxicilina, Meloxicam, Metronidazol, Dexametasona, etc.) |

---

## Fase 4 — Dueños y mascotas

### Módulo 4.1: Owners (Propietarios) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 4.1.1 | Form Request | `app/Http/Requests/Owners/StoreOwnerRequest.php`, `UpdateOwnerRequest.php` |
| 4.1.2 | Actions | `app/Actions/Owners/CreateOwnerAction.php`, `UpdateOwnerAction.php`, `DeleteOwnerAction.php` |
| 4.1.3 | Controller Api | `app/Http/Controllers/Api/Admin/OwnerController.php` |
| 4.1.4 | Rutas API | `routes/api.php` → `admin/owners` |
| 4.1.5 | Resource | `app/Http/Resources/OwnerResource.php` |
| 4.1.6 | Controller Admin | `app/Http/Controllers/Admin/OwnerController.php` (index, create, edit, show) |
| 4.1.7 | Rutas Web | `routes/web.php` → `admin/owners` |
| 4.1.8 | Vista index | `resources/views/admin/owners/index.blade.php` + DataTable |
| 4.1.9 | Vista crear | `resources/views/admin/owners/create.blade.php` |
| 4.1.10 | Vista editar | `resources/views/admin/owners/edit.blade.php` |
| 4.1.11 | Vista show | `resources/views/admin/owners/show.blade.php` (datos del dueño + listado de sus mascotas) |
| 4.1.12 | JS de página | `public/js/pages/owners.js` |
| 4.1.13 | Seeder | `database/seeders/OwnerSeeder.php` (dueños realistas con DNI/CE peruanos) |

### Módulo 4.2: Pacientes (Mascotas) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 4.2.1 | Form Request | `app/Http/Requests/Pacientes/StorePacienteRequest.php`, `UpdatePacienteRequest.php` |
| 4.2.2 | Actions | `app/Actions/Pacientes/CreatePacienteAction.php`, `UpdatePacienteAction.php`, `DeletePacienteAction.php` |
| 4.2.3 | Controller Api | `app/Http/Controllers/Api/Admin/PacienteController.php` |
| 4.2.4 | Rutas API | `routes/api.php` → `admin/pacientes` |
| 4.2.5 | Resource | `app/Http/Resources/PacienteResource.php` |
| 4.2.6 | Controller Admin | `app/Http/Controllers/Admin/PacienteController.php` (index, create, edit, show) |
| 4.2.7 | Rutas Web | `routes/web.php` → `admin/pacientes` |
| 4.2.8 | Vista index | `resources/views/admin/pacientes/index.blade.php` + DataTable |
| 4.2.9 | Vista crear | `resources/views/admin/pacientes/create.blade.php` (select owner, species, breed dinámico por especie) |
| 4.2.10 | Vista editar | `resources/views/admin/pacientes/edit.blade.php` |
| 4.2.11 | Vista show (ficha) | `resources/views/admin/pacientes/show.blade.php` — Ficha completa con tabs: Datos generales · Historial médico · Vacunas · Cirugías |
| 4.2.12 | JS de página | `public/js/pages/pacientes.js` |
| 4.2.13 | JS de ficha | `public/js/pages/paciente-ficha.js` (carga tabs vía AJAX) |
| 4.2.14 | Seeder | `database/seeders/PacienteSeeder.php` (mascotas coherentes con dueños/especies/razas sembradas) |

> **Nota:** El select de razas se filtra dinámicamente según la especie seleccionada (petición AJAX a `api/admin/breeds?species_id=X`). La foto se sube a Cloudinary.

---

## Fase 5 — Horario y citas

### Módulo 5.1: Veterinarian Schedules (Horarios) `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 5.1.1 | Form Request | `app/Http/Requests/VeterinarianSchedules/StoreScheduleRequest.php`, `UpdateScheduleRequest.php` |
| 5.1.2 | Actions | `app/Actions/VeterinarianSchedules/CreateScheduleAction.php`, `UpdateScheduleAction.php`, `DeleteScheduleAction.php` |
| 5.1.3 | Controller Api | `app/Http/Controllers/Api/Admin/VeterinarianScheduleController.php` |
| 5.1.4 | Rutas API | `routes/api.php` → `admin/veterinarian-schedules` |
| 5.1.5 | Resource | `app/Http/Resources/VeterinarianScheduleResource.php` |
| 5.1.6 | Controller Admin | `app/Http/Controllers/Admin/VeterinarianScheduleController.php` |
| 5.1.7 | Rutas Web | `routes/web.php` → `admin/veterinarian-schedules` |
| 5.1.8 | Vista index | `resources/views/admin/veterinarian-schedules/index.blade.php` (tabla semanal por veterinario) |
| 5.1.9 | Vista crear | `resources/views/admin/veterinarian-schedules/create.blade.php` (select veterinario, día, hora inicio/fin) |
| 5.1.10 | Vista editar | `resources/views/admin/veterinarian-schedules/edit.blade.php` |
| 5.1.11 | JS de página | `public/js/pages/veterinarian-schedules.js` |
| 5.1.12 | Seeder | `database/seeders/VeterinarianScheduleSeeder.php` (horarios L-V para veterinarios sembrados) |

### Módulo 5.2: Citas `[S]` `[F]`

| # | Tarea | Archivos |
|---|-------|----------|
| 5.2.1 | Form Request | `app/Http/Requests/Citas/StoreCitaRequest.php`, `UpdateCitaRequest.php` |
| 5.2.2 | Actions | `app/Actions/Citas/CreateCitaAction.php`, `UpdateCitaAction.php`, `CambiarEstadoCitaAction.php`, `DeleteCitaAction.php` |
| 5.2.3 | Filters (Pipeline) | `app/Filters/Citas/FiltrarPorVeterinario.php`, `FiltrarPorFecha.php`, `FiltrarPorEstado.php` |
| 5.2.4 | Controller Api | `app/Http/Controllers/Api/Admin/CitaController.php` (index, store, update, cambiarEstado, destroy) |
| 5.2.5 | Rutas API | `routes/api.php` → `admin/citas` + ruta extra `PATCH admin/citas/{cita}/estado` |
| 5.2.6 | Resource | `app/Http/Resources/CitaResource.php` (incluye paciente, veterinario, servicio) |
| 5.2.7 | Controller Admin | `app/Http/Controllers/Admin/CitaController.php` (index, create, edit, calendar) |
| 5.2.8 | Rutas Web | `routes/web.php` → `admin/citas` + `admin/citas/calendario` |
| 5.2.9 | Vista index | `resources/views/admin/citas/index.blade.php` + DataTable con filtros (veterinario, fecha, estado) |
| 5.2.10 | Vista crear | `resources/views/admin/citas/create.blade.php` (selects de paciente, veterinario, servicio, fecha, hora) |
| 5.2.11 | Vista editar | `resources/views/admin/citas/edit.blade.php` |
| 5.2.12 | Vista calendario | `resources/views/admin/citas/calendar.blade.php` (vista tipo calendario, se puede usar FullCalendar JS vía CDN) |
| 5.2.13 | JS de página | `public/js/pages/citas.js` (DataTable + filtros + cambio de estado AJAX) |
| 5.2.14 | JS de calendario | `public/js/pages/citas-calendar.js` |
| 5.2.15 | Seeder | `database/seeders/CitaSeeder.php` (citas coherentes con pacientes/veterinarios/servicios sembrados) |

> **Nota sobre citas:** La validación debe cruzar con `veterinarian_schedules` para evitar agendar fuera de horario. El cambio de estado es una acción AJAX independiente (botón por fila en DataTable). Si se marca como "completada", se habilita la generación de factura (Fase 7).

---

## Fase 6 — Módulo clínico

### Módulo 6.1: Vacunas `[TX]` `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 6.1.1 | Form Request | `app/Http/Requests/Vacunas/StoreVacunaRequest.php`, `UpdateVacunaRequest.php` |
| 6.1.2 | Action `[TX]` | `app/Actions/Vacunas/CreateVacunaAction.php` — transacción: inserta `vacunas` + inserta `medical_record` (event_type='vacuna') + inserta `reminders` (próxima dosis si `next_due_date` viene) |
| 6.1.3 | Action | `app/Actions/Vacunas/UpdateVacunaAction.php`, `DeleteVacunaAction.php` |
| 6.1.4 | Controller Api | `app/Http/Controllers/Api/Admin/VacunaController.php` |
| 6.1.5 | Rutas API | `routes/api.php` → `admin/vacunas` |
| 6.1.6 | Resource | `app/Http/Resources/VacunaResource.php` |
| 6.1.7 | Controller Admin | `app/Http/Controllers/Admin/VacunaController.php` (index, create, edit) |
| 6.1.8 | Rutas Web | `routes/web.php` → `admin/vacunas` |
| 6.1.9 | Vista index | `resources/views/admin/vacunas/index.blade.php` + DataTable |
| 6.1.10 | Vista crear | `resources/views/admin/vacunas/create.blade.php` (select paciente, veterinario, tipo vacuna, cita opcional, fecha próxima dosis) |
| 6.1.11 | Vista editar | `resources/views/admin/vacunas/edit.blade.php` |
| 6.1.12 | JS de página | `public/js/pages/vacunas.js` |
| 6.1.13 | Seeder | `database/seeders/VacunaSeeder.php` |

### Módulo 6.2: Cirugías (Surgiere) `[TX]` `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 6.2.1 | Form Request | `app/Http/Requests/Cirugias/StoreCirugiaRequest.php`, `UpdateCirugiaRequest.php` |
| 6.2.2 | Action `[TX]` | `app/Actions/Cirugias/CreateCirugiaAction.php` — transacción: inserta `surgiere` + inserta `medical_record` (event_type='cirugia') |
| 6.2.3 | Action | `app/Actions/Cirugias/UpdateCirugiaAction.php`, `CambiarEstadoCirugiaAction.php`, `DeleteCirugiaAction.php` |
| 6.2.4 | Controller Api | `app/Http/Controllers/Api/Admin/CirugiaController.php` (index, store, update, cambiarEstado, destroy) |
| 6.2.5 | Rutas API | `routes/api.php` → `admin/cirugias` + `PATCH admin/cirugias/{cirugia}/estado` |
| 6.2.6 | Resource | `app/Http/Resources/CirugiaResource.php` |
| 6.2.7 | Controller Admin | `app/Http/Controllers/Admin/CirugiaController.php` (index, create, edit) |
| 6.2.8 | Rutas Web | `routes/web.php` → `admin/cirugias` |
| 6.2.9 | Vista index | `resources/views/admin/cirugias/index.blade.php` + DataTable (estado con badge) |
| 6.2.10 | Vista crear | `resources/views/admin/cirugias/create.blade.php` (select paciente, veterinario, tipo cirugía, cita opcional) |
| 6.2.11 | Vista editar | `resources/views/admin/cirugias/edit.blade.php` |
| 6.2.12 | JS de página | `public/js/pages/cirugias.js` |
| 6.2.13 | Seeder | `database/seeders/CirugiaSeeder.php` |

### Módulo 6.3: Medical Records (Historial médico) `[TX]`

| # | Tarea | Archivos |
|---|-------|----------|
| 6.3.1 | Form Request | `app/Http/Requests/MedicalRecords/StoreMedicalRecordRequest.php`, `UpdateMedicalRecordRequest.php` |
| 6.3.2 | Action `[TX]` | `app/Actions/MedicalRecords/CreateMedicalRecordAction.php` — transacción: inserta `medical_record` + `prescriptions` (array) + `vital_signs` si vienen en el mismo submit |
| 6.3.3 | Action | `app/Actions/MedicalRecords/UpdateMedicalRecordAction.php`, `DeleteMedicalRecordAction.php` |
| 6.3.4 | Controller Api | `app/Http/Controllers/Api/Admin/MedicalRecordController.php` |
| 6.3.5 | Rutas API | `routes/api.php` → `admin/medical-records` |
| 6.3.6 | Resource | `app/Http/Resources/MedicalRecordResource.php` (incluye prescriptions, vital_signs, attachments) |
| 6.3.7 | Controller Admin | `app/Http/Controllers/Admin/MedicalRecordController.php` (index, create, show) |
| 6.3.8 | Rutas Web | `routes/web.php` → `admin/medical-records` |
| 6.3.9 | Vista index | `resources/views/admin/medical-records/index.blade.php` (listado general, con filtro por paciente) |
| 6.3.10 | Vista crear | `resources/views/admin/medical-records/create.blade.php` (consulta + recetas + signos vitales en un solo form) |
| 6.3.11 | Vista show | `resources/views/admin/medical-records/show.blade.php` (detalle completo de la entrada) |
| 6.3.12 | JS de página | `public/js/pages/medical-records.js` (formulario dinámico: agregar/quitar filas de recetas) |

### Módulo 6.4: Prescriptions (Recetas)

> Las recetas se crean/editan como parte del `medical_record` (módulo 6.3). No requieren CRUD independiente, pero sí Resource.

| # | Tarea | Archivos |
|---|-------|----------|
| 6.4.1 | Resource | `app/Http/Resources/PrescriptionResource.php` |

### Módulo 6.5: Vital Signs (Signos vitales)

> Los signos vitales se crean como parte del `medical_record` (módulo 6.3). No requieren CRUD independiente, pero sí Resource.

| # | Tarea | Archivos |
|---|-------|----------|
| 6.5.1 | Resource | `app/Http/Resources/VitalSignResource.php` |

### Módulo 6.6: Medical Record Attachments (Adjuntos)

| # | Tarea | Archivos |
|---|-------|----------|
| 6.6.1 | Form Request | `app/Http/Requests/MedicalRecordAttachments/StoreAttachmentRequest.php` |
| 6.6.2 | Action | `app/Actions/MedicalRecordAttachments/UploadAttachmentAction.php`, `DeleteAttachmentAction.php` |
| 6.6.3 | Controller Api | `app/Http/Controllers/Api/Admin/MedicalRecordAttachmentController.php` (store, destroy) |
| 6.6.4 | Rutas API | `routes/api.php` → `admin/medical-records/{record}/attachments` |
| 6.6.5 | Resource | `app/Http/Resources/MedicalRecordAttachmentResource.php` |
| 6.6.6 | JS integrado | En `public/js/pages/medical-records.js` (subida de archivos vía FormData + Cloudinary) |

---

## Fase 7 — Facturación y pagos

### Módulo 7.1: Invoices (Facturas) `[TX]` `[F]` `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 7.1.1 | Form Request | `app/Http/Requests/Invoices/StoreInvoiceRequest.php`, `UpdateInvoiceRequest.php` |
| 7.1.2 | Action `[TX]` | `app/Actions/Facturacion/GenerarInvoiceAction.php` — transacción: inserta `invoices` + primer `payment` (si se paga al crear) |
| 7.1.3 | Action | `app/Actions/Facturacion/AnularInvoiceAction.php` |
| 7.1.4 | Filters (Pipeline) | `app/Filters/Facturas/FiltrarPorEstado.php`, `FiltrarPorRangoFecha.php`, `FiltrarPorOwner.php` |
| 7.1.5 | Controller Api | `app/Http/Controllers/Api/Admin/InvoiceController.php` (index, store, show, anular) |
| 7.1.6 | Rutas API | `routes/api.php` → `admin/invoices` + `PATCH admin/invoices/{invoice}/anular` |
| 7.1.7 | Resource | `app/Http/Resources/InvoiceResource.php` (incluye payments, invoiceable) |
| 7.1.8 | Controller Admin | `app/Http/Controllers/Admin/InvoiceController.php` (index, create, show) |
| 7.1.9 | Rutas Web | `routes/web.php` → `admin/invoices` |
| 7.1.10 | Vista index | `resources/views/admin/facturacion/invoices/index.blade.php` + DataTable con filtros (estado, rango fecha) |
| 7.1.11 | Vista crear | `resources/views/admin/facturacion/invoices/create.blade.php` (select tipo invoiceable + id, monto, primer pago opcional) |
| 7.1.12 | Vista show | `resources/views/admin/facturacion/invoices/show.blade.php` (detalle factura + lista de pagos + botón "Registrar pago") |
| 7.1.13 | JS de página | `public/js/pages/invoices.js` |

### Módulo 7.2: Payments (Pagos) `[TX]`

| # | Tarea | Archivos |
|---|-------|----------|
| 7.2.1 | Form Request | `app/Http/Requests/Payments/StorePaymentRequest.php` |
| 7.2.2 | Action `[TX]` | `app/Actions/Facturacion/RegistrarPagoAction.php` — transacción: inserta `payments` + actualiza `remaining_balance` y `status` del `invoice` |
| 7.2.3 | Action | `app/Actions/Facturacion/AnularPagoAction.php` |
| 7.2.4 | Controller Api | `app/Http/Controllers/Api/Admin/PaymentController.php` (store, anular) |
| 7.2.5 | Rutas API | `routes/api.php` → `admin/invoices/{invoice}/payments` + `PATCH admin/payments/{payment}/anular` |
| 7.2.6 | Resource | `app/Http/Resources/PaymentResource.php` |
| 7.2.7 | JS integrado | En `public/js/pages/invoices.js` (modal de registro de pago dentro de la vista show de factura) |

### Módulo 7.3: Seeders de facturación `[S]`

| # | Tarea | Archivos |
|---|-------|----------|
| 7.3.1 | Seeder | `database/seeders/InvoiceSeeder.php` (facturas asociadas a citas completadas) |
| 7.3.2 | Seeder | `database/seeders/PaymentSeeder.php` (pagos coherentes con facturas) |

---

## Fase 8 — Recordatorios

### Módulo 8.1: Reminders (Recordatorios)

| # | Tarea | Archivos |
|---|-------|----------|
| 8.1.1 | Form Request | `app/Http/Requests/Reminders/UpdateReminderRequest.php` (solo para cambiar estado) |
| 8.1.2 | Actions | `app/Actions/Reminders/CambiarEstadoReminderAction.php` (pendiente → enviado / cancelado) |
| 8.1.3 | Controller Api | `app/Http/Controllers/Api/Admin/ReminderController.php` (index, cambiarEstado) |
| 8.1.4 | Rutas API | `routes/api.php` → `admin/reminders` + `PATCH admin/reminders/{reminder}/estado` |
| 8.1.5 | Resource | `app/Http/Resources/ReminderResource.php` (incluye paciente, tipo remindable) |
| 8.1.6 | Controller Admin | `app/Http/Controllers/Admin/ReminderController.php` (index) |
| 8.1.7 | Rutas Web | `routes/web.php` → `admin/reminders` |
| 8.1.8 | Vista index | `resources/views/admin/reminders/index.blade.php` + DataTable (pendientes primero, con badge de estado) |
| 8.1.9 | JS de página | `public/js/pages/reminders.js` |

> **Nota:** Los reminders se crean automáticamente desde las acciones de Fase 6 (vacunas y citas). Aquí solo se construye el listado y la gestión de estado.

---

## Tareas transversales post-fase

### Seeders: orden de ejecución en `DatabaseSeeder.php`

```
1. PermissionsDemoSeeder     (ya existe)
2. BranchSeeder
3. UserSeeder                (depende de branches + roles)
4. SpeciesSeeder
5. BreedSeeder               (depende de species)
6. ServiceSeeder
7. VaccineTypeSeeder         (depende de species)
8. MedicineSeeder
9. OwnerSeeder
10. PacienteSeeder           (depende de owners, species, breeds)
11. VeterinarianScheduleSeeder (depende de users veterinarios)
12. CitaSeeder               (depende de pacientes, users, services)
13. VacunaSeeder             (depende de pacientes, users, vaccine_types, citas)
14. CirugiaSeeder            (depende de pacientes, users, citas)
15. InvoiceSeeder            (depende de citas/vacunas/cirugías, owners)
16. PaymentSeeder            (depende de invoices)
```

> Los seeders de `medical_record`, `prescriptions`, `vital_signs` y `reminders` se generan como efecto secundario de las transacciones de vacunas/cirugías en los seeders correspondientes — no necesitan seeder aparte.

### Cablear sidebar

Al terminar cada fase, actualizar los `href="#"` del sidebar en `app.blade.php` con las rutas reales (`{{ route('admin.X.index') }}`).

---

## Resumen de conteo por fase

| Fase | Módulos | Archivos nuevos estimados |
|------|---------|--------------------------|
| 0    | 3 (infra) | ~5 |
| 2    | 2 (branches, users) | ~24 |
| 3    | 5 (species, breeds, services, vaccine_types, medicines) | ~60 |
| 4    | 2 (owners, pacientes) | ~28 |
| 5    | 2 (schedules, citas) | ~30 |
| 6    | 6 (vacunas, cirugías, medical_records, prescriptions, vital_signs, attachments) | ~40 |
| 7    | 3 (invoices, payments, seeders) | ~22 |
| 8    | 1 (reminders) | ~9 |
| **Total** | **24 módulos** | **~218 archivos** |
