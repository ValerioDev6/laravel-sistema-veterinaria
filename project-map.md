# Project Map — Sistema Veterinaria

> Fuente de verdad del estado real del sistema. Se actualiza al terminar cada fase.
> Si hay discrepancia entre el código y este archivo, el código manda y el archivo se corrige.

---

## Estado general

| Fase | Estado | Fecha completada |
|------|--------|-----------------|
| 1 — Auth + RBAC + Layout Velzon | ✅ Completada (pre-existente) | — |
| 2 — Sedes y personal | ✅ Completada | 2026-08-06 |
| 3 — Catálogos base | ✅ Completada | 2026-08-06 |
| 4 — Dueños y mascotas | ✅ Completada | 2026-08-06 |
| 5 — Horario y citas | ✅ Completada | 2026-08-06 |
| 6 — Módulo clínico | ✅ Completada | 2026-08-06 |
| 7 — Facturación y pagos | ✅ Completada | 2026-08-06 |
| 8 — Recordatorios | ✅ Completada | 2026-08-11 |
| 9 — Reportes | 🚫 Fuera de alcance | |
| Roles y Permisos | ✅ Completada | 2026-08-12 |

---

## Infraestructura existente (Fase 1)

### Auth y permisos
- Laravel Breeze (stack blade) — login, registro, logout, reset password
- Spatie Laravel Permission — roles: `Super-Admin`, `Veterinario`, `Recepcionista`
- Guard `api` con driver `session`
- `Gate::before` para Super-Admin
- API autenticada por sesión (Sanctum stateful, `$middleware->statefulApi()` en `bootstrap/app.php`); los dominios stateful se definen en `.env` con `SANCTUM_STATEFUL_DOMAINS` (incluye `localhost:8000` — fix del Prompt #29; sin ello el navegador en `localhost:8000` recibe 401 `Unauthenticated` en toda la API).
- **NO TOCAR**

### Layout
- `resources/views/layouts/app.blade.php` — Velzon, sidebar con menú completo (hrefs en `#`)
- `resources/views/layouts/guest.blade.php` — layout público (auth)
- `resources/views/dashboard.blade.php` — placeholder vacío, no se toca

### Modelos Eloquent existentes (20 de negocio)
- `Branch`, `User`, `Species`, `Breed`, `Owner`, `Paciente`
- `VeterinarianSchedule`, `Service`, `VaccineType`, `Cita`
- `Vacuna`, `Surgiere`, `MedicalRecord`, `Medicine`
- `Prescription`, `VitalSign`, `MedicalRecordAttachment`
- `Invoice`, `Payment`, `Reminder`

### Seeders existentes
- `PermissionsDemoSeeder` — roles y permisos base

### Rutas existentes
- `routes/web.php` → `/`, `/dashboard`, `/profile`
- `routes/api.php` → `/user` (Sanctum)
- `routes/auth.php` → login, registro, etc.

---

## Fase 2 — Sedes y personal

### Módulo: Branches
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Branches/StoreBranchRequest.php`, `UpdateBranchRequest.php` | ✅ |
| Actions | `app/Actions/Branches/CreateBranchAction.php`, `UpdateBranchAction.php`, `DeleteBranchAction.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/BranchController.php` (index, show, store, update, destroy) | ✅ |
| Resource | `app/Http/Resources/BranchResource.php` | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/BranchController.php` (index, create, edit) | ✅ |
| Vistas | `admin/branches/{index,create,edit}.blade.php` | ✅ |
| JS | `public/js/pages/branches.js` | ✅ |
| Seeder | `database/seeders/BranchSeeder.php` (3 sedes Lima) | ✅ |

### Módulo: Users
> 2026-08-24 (Prompt #31): módulo genérico de personal (usuario/password). La gestión de horarios del veterinario vive en el módulo **Veterinarios**. El backend conserva TX + sync/borrado por cambio de rol.
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Users/StoreUserRequest.php`, `UpdateUserRequest.php` (decodifican JSON `schedules`; valida franjas por día) | ✅ |
| Actions | `app/Actions/Users/CreateUserAction.php` [TX], `UpdateUserAction.php` [TX], `ToggleUserStatusAction.php`, `DeleteUserAction.php` (los horarios NO bloquean borrado: se eliminan en cascada), `SincronizarHorariosAction.php` (borra+reinserción de franjas; se llama desde Create/Update cuando el rol es `Veterinario`) | ✅ |
| Image uploader | `app/Support/ImageUploader.php` (Cloudinary si se configura, local si no) | ✅ |
| Migration is_active | `database/migrations/2026_08_06_010000_add_is_active_to_users_table.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/UserController.php` (index, store, update, toggleStatus, destroy) | ✅ |
| Resource | `app/Http/Resources/UserResource.php` (incluye roles) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/UserController.php` (index, create, edit, movimientos) | ✅ |
| Vistas | `admin/usuarios/{index,create,edit,movimientos}.blade.php` (genéricas, sin horarios; JS versionado `usuarios.js?v=3`) | ✅ |
| JS | `public/js/pages/usuarios.js` | ✅ |
| Seeder | `database/seeders/UserSeeder.php` (3 vet + 2 recepcionistas) | ✅ |

---

### Módulo: Veterinarios
> 2026-08-24 (Prompt #31): módulo dedicado a veterinarios con su **horario tipo matriz checkbox** (Lun-Sáb × 07:00-18:30, extensión dinámica si la precarga supera el cierre; casillas contiguas se fusionan en franjas al guardar). Entrada del sidebar en la sección **Clínica** (primer ítem: los vets atienden citas/vacunas/cirugías), NO en Configuración — decisión del usuario tras dos reubicaciones.
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Veterinarios/{StoreVeterinarioRequest,UpdateVeterinarioRequest}.php` — heredan de Users con `role` nullable (el controller fuerza rol `Veterinario`) | ✅ |
| Action List | `app/Actions/Veterinarios/ListVeterinariosAction.php` — Pipeline + `role("Veterinario")` + eager `branch`, `veterinarian_schedules` | ✅ |
| Resource | `app/Http/Resources/VeterinarioResource.php` — extiende UserResource + `horarios` + `edit_url` propio | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/VeterinarioController.php` (index, create, edit con `abort_unless(hasRole)` y precarga de horarios) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/VeterinarioController.php` (CRUD reutilizando Actions de Users; rol forzado server-side) | ✅ |
| Vistas | `admin/veterinarios/{index,create,edit}.blade.php` (index DataTable solo vets con columna "Horario de atención"; create/edit con matriz `#tablaHorarioVeterinario` generada por JS + resumen horas/semana; edit inyecta `window.veterinariosHorariosInit`) | ✅ |
| JS | `public/js/pages/veterinarios.js` (DataTable server-side; matriz: filas base 07:00→18:30 + extensión de 30 min según precarga, cabecera día-completo con indeterminate, fusión de casillas contiguas → JSON `schedules`, errores 422 en card) | ✅ |

---

## Fase 3 — Catálogos base

### Módulo: Species
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Species/StoreSpeciesRequest.php`, `UpdateSpeciesRequest.php` | ✅ |
| Actions | `app/Actions/Species/CreateSpeciesAction.php`, `UpdateSpeciesAction.php`, `DeleteSpeciesAction.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/SpeciesController.php` (index, show, store, update, destroy) | ✅ |
| Resource | `app/Http/Resources/SpeciesResource.php` | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/SpeciesController.php` (index, create, edit) | ✅ |
| Vistas | `admin/species/{index,create,edit}.blade.php` | ✅ |
| JS | `public/js/pages/species.js` | ✅ |
| Seeder | `database/seeders/SpeciesSeeder.php` (6 especies, sembrado) | ✅ |

### Módulo: Breeds
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Breeds/StoreBreedRequest.php`, `UpdateBreedRequest.php` (unique compuesto species_id+name) | ✅ |
| Actions | `app/Actions/Breeds/CreateBreedAction.php`, `UpdateBreedAction.php`, `DeleteBreedAction.php` (valida pacientes) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/BreedController.php` (con `with("species")`) | ✅ |
| Resource | `app/Http/Resources/BreedResource.php` (incluye species name) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/BreedController.php` (index, create, edit) | ✅ |
| Vistas | `admin/breeds/{index,create,edit}.blade.php` | ✅ |
| JS | `public/js/pages/breeds.js` | ✅ |
| Seeder | `database/seeders/BreedSeeder.php` (29 razas por especie, sembrado) | ✅ |

### Módulo: Services
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Services/StoreServiceRequest.php`, `UpdateServiceRequest.php` (category enum, base_price, duration) | ✅ |
| Actions | `app/Actions/Services/CreateServiceAction.php`, `UpdateServiceAction.php`, `DeleteServiceAction.php` (valida citas) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/ServiceController.php` (index, show, store, update, destroy) | ✅ |
| Resource | `app/Http/Resources/ServiceResource.php` | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/ServiceController.php` (index, create, edit) | ✅ |
| Vistas | `admin/services/{index,create,edit}.blade.php` | ✅ |
| JS | `public/js/pages/services.js` | ✅ |
| Seeder | `database/seeders/ServiceSeeder.php` (7 servicios, sembrado) | ✅ |

### Módulo: Vaccine Types
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/VaccineTypes/StoreVaccineTypeRequest.php`, `UpdateVaccineTypeRequest.php` (species_id nullable, base_price; Update usa `route('vaccine_type')` para la regla unique) | ✅ |
| Actions | `app/Actions/VaccineTypes/CreateVaccineTypeAction.php`, `UpdateVaccineTypeAction.php`, `DeleteVaccineTypeAction.php` (valida vacunas) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/VaccineTypeController.php` (index, show, store, update, destroy) | ✅ |
| Resource | `app/Http/Resources/VaccineTypeResource.php` (incluye species name + base_price) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/VaccineTypeController.php` (index, create, edit) | ✅ |
| Vistas | `admin/vaccine-types/{index,create,edit}.blade.php` (columna/input Precio) | ✅ |
| JS | `public/js/pages/vaccine-types.js` (recolecta base_price) | ✅ |
| Seeder | `database/seeders/VaccineTypeSeeder.php` (7 tipos con base_price 35–70, sembrado) | ✅ |

### Módulo: Medicines
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Medicines/StoreMedicineRequest.php`, `UpdateMedicineRequest.php` (name, quantity, unit_cost) | ✅ |
| Actions | `app/Actions/Medicines/CreateMedicineAction.php`, `UpdateMedicineAction.php`, `DeleteMedicineAction.php` (valida prescripciones) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/MedicineController.php` (index, show, store, update, destroy) | ✅ |
| Resource | `app/Http/Resources/MedicineResource.php` | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/MedicineController.php` (index, create, edit) | ✅ |
| Vistas | `admin/medicines/{index,create,edit}.blade.php` | ✅ |
| JS | `public/js/pages/medicines.js` | ✅ |
| Seeder | `database/seeders/MedicineSeeder.php` (6 medicamentos, sembrado) | ✅ |

---

## Fase 4 — Dueños y mascotas

### Módulo: Owners
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Owners/StoreOwnerRequest.php`, `UpdateOwnerRequest.php` (DNI/CE/Pasaporte) | ✅ |
| Actions | `app/Actions/Owners/CreateOwnerAction.php`, `UpdateOwnerAction.php`, `DeleteOwnerAction.php` (valida mascotas) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/OwnerController.php` (index, show, store, update, destroy + withCount pacientes) | ✅ |
| Resource | `app/Http/Resources/OwnerResource.php` (full_name, pacientes_count, show_url) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/OwnerController.php` (index, create, edit, show) | ✅ |
| Vistas | `admin/owners/{index,create,edit,show}.blade.php` (show: datos + mascotas) | ✅ |
| JS | `public/js/pages/owners.js` | ✅ |
| Seeder | `database/seeders/OwnerSeeder.php` (7 dueños, sembrado) | ✅ |

### Módulo: Pacientes
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Pacientes/StorePacienteRequest.php`, `UpdatePacienteRequest.php` (photo image, gender enum) | ✅ |
| Actions | `app/Actions/Pacientes/CreatePacienteAction.php`, `UpdatePacienteAction.php` (suben foto), `DeletePacienteAction.php` (valida citas + borra foto) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/PacienteController.php` (CRUD + records/vacunas/cirugias para ficha) | ✅ |
| Resource | `app/Http/Resources/PacienteResource.php` (owner, species, breed) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/PacienteController.php` (index, create, edit, show) | ✅ |
| Vistas | `admin/pacientes/{index,create,edit,show}.blade.php` (create/edit con breed dinámico; show con tabs) | ✅ |
| JS | `public/js/pages/pacientes.js` + `public/js/pages/paciente-ficha.js` (tabs vía AJAX) | ✅ |
| Seeder | `database/seeders/PacienteSeeder.php` (10 mascotas, sembrado) | ✅ |

---

## Fase 5 — Horario y citas

### Módulo: Veterinarian Schedules — ❌ ELIMINADO 2026-08-24 (Prompt #30)
> El CRUD standalone fue eliminado y su función vive hoy en el **módulo Veterinarios** (`admin/veterinarios`, Prompt #31) con matriz checkbox de horario. Se conservan: el modelo `VeterinarianSchedule`, su seeder y la cascada de `DeleteUserAction`. La disponibilidad de citas/vacunas/cirugías sigue leyendo de `veterinarian_schedules` sin cambios.
| Elemento | Estado |
|----------|--------|
| Rutas web + API (`veterinarian-schedules`) | 🗑️ Eliminadas |
| Controllers Admin/Api, Actions, Requests, Resource | 🗑️ Eliminados |
| Vistas `admin/veterinarian-schedules/*` + JS de página | 🗑️ Eliminados |
| Entrada "Horarios" del sidebar | 🗑️ Eliminada |

### Módulo: Citas
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Citas/StoreCitaRequest.php`, `UpdateCitaRequest.php` (valida contra `veterinarian_schedules` + cruce de citas) | ✅ |
| Actions | `app/Actions/Citas/CreateCitaAction.php`, `UpdateCitaAction.php`, `CambiarEstadoCitaAction.php`, `DeleteCitaAction.php` (valida historial) | ✅ |
| Filters | `app/Filters/Citas/{FiltrarPorVeterinario,FiltrarPorFecha,FiltrarPorEstado}.php` + `app/Filters/Pipeline.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/CitaController.php` (index con pipeline, store, update, cambiarEstado, destroy, disponibilidad, calendario) | ✅ |
| Resource | `app/Http/Resources/CitaResource.php` (paciente, veterinario, servicio) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/CitaController.php` (index, create, edit, calendar) | ✅ |
| Vistas | `admin/citas/{index,create,edit,calendar}.blade.php` (index con filtros; calendario FullCalendar global + drawer derecho con estado editable) | ✅ |
| JS | `public/js/pages/citas.js` + `public/js/pages/citas-calendar.js` | ✅ |
| Seeder | `database/seeders/CitaSeeder.php` (8 citas, sembrado) | ✅ |
| Modelo | `app/Models/Cita.php` (relación `veterinarian()` agregada) | ✅ |

---

## Fase 6 — Módulo clínico ✅ 2026-08-06

### Módulo: Vacunas
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Vacunas/StoreVacunaRequest.php`, `UpdateVacunaRequest.php` (`vaccine_type_id` o `new_vaccine_type` con name/base_price/species_id; fecha: en update permite la fecha original aunque sea pasada; `after()` con ValidarDisponibilidadVacuna) | ✅ |
| Action [TX] | `app/Actions/Vacunas/CreateVacunaAction.php` (vacuna + medical_record + reminder + invoice/pago; crea `VaccineType` inline si viene `new_vaccine_type`) | ✅ |
| Actions | `app/Actions/Vacunas/UpdateVacunaAction.php` (TX + upsert invoice/pago), `DeleteVacunaAction.php` (valida `medical_records`) | ✅ |
| Acción disponibilidad | `app/Actions/Vacunas/ValidarDisponibilidadVacuna.php` (horario activo + conflicto cita/vacuna, `ignorarVacunaId`) | ✅ |
| Filtros | `app/Filters/Vacunas/` (Busqueda, Especie, Veterinario, EstadoPago, Fecha) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/VacunaController.php` (CRUD + `disponibilidad` + `show`) | ✅ |
| Resource | `app/Http/Resources/VacunaResource.php` (species, vaccination_time, vaccine_price, payment_status/total/paid) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/VacunaController.php` (index con filtros; create/edit con disponibilidad + pago) | ✅ |
| Vistas | `admin/vacunas/{index,create,edit}.blade.php` (filtros, bloque disponibilidad, preview mascota, sección Pago) | ✅ |
| JS | `public/js/pages/vacunas.js` (DataTable serverSide + filtros + `initVacunaForm` disponibilidad/pago) | ✅ |
| Seeder | `database/seeders/VacunaSeeder.php` (6 vacunas con vaccination_time, sembrado) | ✅ |
| Migración | `2026_08_11_000001_add_time_and_price_to_vacunas_and_vaccine_types.php` (vaccination_time TIME, base_price) | ✅ |
| Rutas | Web `admin.vacunas.*` + API `admin.api.vacunas.*` (+ `api/admin/vacunas/disponibilidad`) | ✅ |

### Módulo: Cirugías (Surgiere)
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Cirugias/StoreCirugiaRequest.php`, `UpdateCirugiaRequest.php` (status enum; total + payment_method + advance_amount) | ✅ |
| Action [TX] | `app/Actions/Cirugias/CreateCirugiaAction.php` (surgiere + `medical_record` event_type='cirugia' + invoice/pago; arma fecha desde `surgery_date`+`surgery_time`) | ✅ |
| Actions | `app/Actions/Cirugias/UpdateCirugiaAction.php` (upsert invoice/pago), `ObtenerDisponibilidadCirugiaAction.php` (vets + horario + slots, agenda unificada citas/vacunas/cirugías), `ValidarDisponibilidadCirugia.php`, `CambiarEstadoCirugiaAction.php`, `CambiarEstadoPagoCirugiaAction.php`, `DeleteCirugiaAction.php` (valida `medical_records`) | ✅ |
| Filters | `app/Filters/Cirugias/` (busqueda, especie, veterinario, estado de pago, fecha) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/CirugiaController.php` (CRUD + cambiarEstado + cambiarEstadoPago + disponibilidad) | ✅ |
| Resource | `app/Http/Resources/CirugiaResource.php` (species, surgery_time, payment_status/total/paid) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/CirugiaController.php` (index con filtros; create/edit con disponibilidad + pago) | ✅ |
| Vistas | `admin/cirugias/{index,create,edit}.blade.php` (filtros, bloque disponibilidad con vet-cards + horas, preview mascota, bloque Pago) | ✅ |
| JS | `public/js/pages/cirugias.js` (DataTable serverSide + filtros + `initCirugiaForm` disponibilidad/pago) | ✅ |
| Seeder | `database/seeders/CirugiaSeeder.php` (3 cirugías, sembrado) | ✅ |
| Rutas | Web `admin.cirugias.*` + API `admin.api.cirugias.*` (+ `disponibilidad`, PATCH `/estado`, PATCH `/estado-pago`) | ✅ |

### Módulo: Medical Records
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/MedicalRecords/StoreMedicalRecordRequest.php`, `UpdateMedicalRecordRequest.php` | ✅ |
| Action [TX] | `app/Actions/MedicalRecords/CreateMedicalRecordAction.php` (record + prescriptions + vital_signs) | ✅ |
| Actions | `app/Actions/MedicalRecords/UpdateMedicalRecordAction.php`, `DeleteMedicalRecordAction.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/MedicalRecordController.php` (index con filtro `pet_id`) | ✅ |
| Resource | `app/Http/Resources/MedicalRecordResource.php` (prescriptions, vital_signs, attachments, show_url/edit_url) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/MedicalRecordController.php` (index, create, show) | ✅ |
| Vistas | `admin/medical-records/{index,create,show}.blade.php` (create con recetas dinámicas y signos vitales) | ✅ |
| JS | `public/js/pages/medical-records.js` (DataTable + filas de receta dinámicas + adjuntos) | ✅ |
| Seeder | `database/seeders/MedicalRecordSeeder.php` (8 entradas, 5 signos vitales, 2 prescripciones, sembrado) | ✅ |
| Rutas | Web `admin.medical-records.*` + API `admin.api.medical-records.*` | ✅ |

### Módulo: Prescriptions
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Resource | `app/Http/Resources/PrescriptionResource.php` (medicine, dosage, duration_days) | ✅ |

### Módulo: Vital Signs
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Resource | `app/Http/Resources/VitalSignResource.php` | ✅ |

### Módulo: Medical Record Attachments
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request | `app/Http/Requests/MedicalRecordAttachments/StoreAttachmentRequest.php` (jpeg/png/webp/pdf ≤5MB) | ✅ |
| Actions | `app/Actions/MedicalRecordAttachments/UploadAttachmentAction.php` (ImageUploader, carpeta `medical-records/{id}`), `DeleteAttachmentAction.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/MedicalRecordAttachmentController.php` (store/destroy) | ✅ |
| Resource | `app/Http/Resources/MedicalRecordAttachmentResource.php` | ✅ |
| Rutas API | `POST admin-api/medical-records/{record}/adjuntos`, `DELETE admin-api/adjuntos/{attachment}` | ✅ |
| JS | Integrado en `public/js/pages/medical-records.js` (subida + eliminación en vista show) | ✅ |

**Decisiones técnicas Fase 6:**
- Las entradas clínicas usan **transacciones** (`DB::transaction()`): `CreateVacunaAction` y `CreateCirugiaAction` crean además el `medical_record` correspondiente; el `reminder` solo se genera vía Action (los seeders insertan directo, por eso no crean medical_records/reminders).
- `medical_record.event_type` acepta `consulta, vacuna, cirugia, otro`; las prescripciones y signos vitales se crean/actualizan en la misma transacción (prescripciones se reemplazan al actualizar).
- Signos vitales 1:1 con el record (`updateOrCreate` por `medical_record_id`).
- Adjuntos: tipo inferido por extensión (imagen/pdf), subida local a `storage/app/public/uploads/medical-records/{id}` con fallback Cloudinary vía `ImageUploader`.
- Sidebar: bloques Vacunas y Cirugías cableados; nueva entrada "Historial Clínico" → `admin.medical-records.index`.
- `DatabaseSeeder` ahora ejecuta el pipeline completo (PermissionsDemoSeeder → … → MedicalRecordSeeder) y es **idempotente** (`firstOrCreate`/`updateOrCreate`; `PermissionsDemoSeeder` corregido: ya no lanza `PermissionAlreadyExists` en re-runs).

---

## Fase 7 — Facturación y pagos ✅ 2026-08-06

### Módulo: Invoices
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request Store/Update | `app/Http/Requests/Invoices/StoreInvoiceRequest.php`, `UpdateInvoiceRequest.php` (invoiceable cita/vacuna/surgiere, primer pago condicional) | ✅ |
| Action [TX] | `app/Actions/Facturacion/GenerarInvoiceAction.php` (invoice + primer payment, recalcula status) | ✅ |
| Action | `app/Actions/Facturacion/AnularInvoiceAction.php` (bloquea si hay pagos pagados) | ✅ |
| Filters | `app/Filters/Facturas/{FiltrarPorEstado,FiltrarPorRangoFecha,FiltrarPorOwner}.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/InvoiceController.php` (index con pipeline, store, show, update, anular) | ✅ |
| Resource | `app/Http/Resources/InvoiceResource.php` (owner_name, invoiceable_label, payments, show_url) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/InvoiceController.php` (index, create, show) | ✅ |
| Vistas | `admin/facturacion/invoices/{index,create,show}.blade.php` (index con filtros; show con tabla de pagos y registrar pago) | ✅ |
| JS | `public/js/pages/invoices.js` (DataTable con filtros + casos de la vista show) | ✅ |
| Rutas | Web `admin.invoices.*` + API `admin.api.invoices.*` (+ PATCH `/anular`) | ✅ |

### Módulo: Payments
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request | `app/Http/Requests/Payments/StorePaymentRequest.php` (amount, payment_method, paid_at) | ✅ |
| Action [TX] | `app/Actions/Facturacion/RegistrarPagoAction.php` (payment + recalcula remaining_balance/status de invoice) | ✅ |
| Action | `app/Actions/Facturacion/AnularPagoAction.php` (anula pago y recalcula invoice) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/PaymentController.php` (store dentro de invoice, anular) | ✅ |
| Resource | `app/Http/Resources/PaymentResource.php` | ✅ |
| Rutas API | `POST admin.api.invoices.{invoice}.payments.store`, PATCH `admin.api.payments.anular` | ✅ |
| JS | Integrado en `public/js/pages/invoices.js` (registro + anulación en vista show) | ✅ |

### Seeders de facturación
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Seeder | `database/seeders/InvoiceSeeder.php` (facturas desde citas completadas, vacunas y cirugías completadas, idempotente) | ✅ |
| Seeder | `database/seeders/PaymentSeeder.php` (marca pagado/parcial/pendiente y recalcula remaining_balance) | ✅ |

**Decisiones técnicas Fase 7:**
- `total`, `remaining_balance` decimal(10,2); estado recalculado por las Actions de pago (pagado/parcial/pendiente) y por anulación (anulado).
- `InvoiceResource::invoiceableLabel()` resuelve el nombre de la mascota vía relación `paciente()` según el tipo polimórfico.
- Facturas polimórficas a cita/vacuna/surgiere; el `owner_id` lo selecciona el usuario al generar la factura.
- El seeder de facturas solo factura servicios con estado `completada` (citas/cirugías) y todas las vacunas; montos fijos base por tipo.
- Sidebar: bloque Facturas cableado (Listado/Nueva); "Pagos" enlaza a `admin.invoices.index#pagos`.
- `DatabaseSeeder` ahora agrega al final el pipeline completo incluyendo `InvoiceSeeder` → `PaymentSeeder` (idempotente).

---

## Fase 8 — Recordatorios ✅ 2026-08-11

### Módulo: Reminders
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Form Request | `app/Http/Requests/Reminders/UpdateReminderRequest.php` (solo `status` allowed) | ✅ |
| Action [estado] | `app/Actions/Reminders/CambiarEstadoReminderAction.php` (pendiente → enviado/cancelado/pendiente) | ✅ |
| Action [listado] | `app/Actions/Reminders/ListRemindersAction.php` (Pipeline único dueño de la query) | ✅ |
| Filters | `app/Filters/Reminders/{FiltrarPorBusquedaReminders,FiltrarPorTipoReminder,FiltrarPorPetReminder,FiltrarPorEstadoReminder}.php` | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/ReminderController.php` (index con pipeline, cambiarEstado) | ✅ |
| Resource | `app/Http/Resources/ReminderResource.php` (paciente, species, owner_name, type_label) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/ReminderController.php` (index con pacientes para filtro) | ✅ |
| Vistas | `admin/reminders/index.blade.php` (search + filtros mascota/tipo/estado + badge de estado) | ✅ |
| JS | `public/js/pages/reminders.js` (DataTable serverSide + filtros + cambiar estado) | ✅ |
| Seeder | `database/seeders/ReminderSeeder.php` (demo 11: cita/vacuna/cirugia, pendiente/enviado, idempotente) | ✅ |
| Rutas | Web `admin.reminders.index` + API `admin.api.reminders.index` / `admin.api.reminders.estado` | ✅ |
| Sidebar | Entrada "Recordatorios" → `admin.reminders.index` en sección Clínica | ✅ |

**Decisiones técnicas Fase 8:**
- Búsqueda considera el **texto del reminder** (`message`), el **tipo** (`remindable_type`) y el **nombre de la mascota** (vía `whereHas('paciente')`), cubriendo la relación con Paciente.
- Filtros dedicados: `tipo` (cita/vacuna/cirugia; `surgiere` también mapea a Cirugía), `pet_id`, `status` (pendiente/enviado/cancelado). Orden por defecto: `remind_at` asc (próximos primero).
- El estado se cambia vía PATCH `reminders/{reminder}/estado`; botones por fila: marcar enviado / cancelado / volver a pendiente.
- Los reminders **no** se crean en este módulo: se generan como efecto secundario de las transacciones de Fase 6/5 (`CreateVacunaAction` → tipo `vacuna`). El `ReminderSeeder` inserta demo directo (por eso no pasa por la Action, igual que los otros seeders).
- `ReminderResource::typeLabel()` reutiliza el match de InvoiceResource (`cita`→Cita, `vacuna`→Vacuna, `cirugia`/`surgiere`→Cirugía).

---

## Módulo Roles y Permisos ✅ 2026-08-12

> Gestión de roles (Spatie Laravel Permission) con asignación de permisos. Agregado a petición del usuario; no estaba en el plan original. No se tocó la protección de los demás módulos (las rutas admin siguen pidiendo solo `auth`).

### Elementos
| Elemento | Archivo | Estado |
|----------|---------|--------|
| Catálogo de permisos | `app/Support/PermissionCatalog.php` (16 grupos por módulo + etiquetas en español; desconocidos → "Otros") | ✅ |
| Form Request Store/Update | `app/Http/Requests/Roles/StoreRoleRequest.php`, `UpdateRoleRequest.php` (name único por `guard_name=api`, Update ignora rol actual) | ✅ |
| Actions | `app/Actions/Roles/{CreateRoleAction,UpdateRoleAction,DeleteRoleAction,ListRolesAction}.php` | ✅ |
| Action | `app/Actions/Permisos/ListPermissionsAction.php` | ✅ |
| Filter | `app/Filters/Permisos/FiltrarPorGrupoPermiso.php` (filtra por módulo del catálogo; "Otros" = fuera del catálogo) | ✅ |
| Controller Api | `app/Http/Controllers/Api/Admin/RoleController.php` (index + store/update/destroy), `Api/Admin/PermissionController.php` (index) | ✅ |
| Resources | `app/Http/Resources/RoleResource.php` (name, guard, permissions_count, users_count, is_super_admin, edit_url), `PermissionResource.php` (grupo, label, roles_count) | ✅ |
| Controller Admin | `app/Http/Controllers/Admin/RoleController.php` (index/create/edit; `gruposConPermisos()`), `Admin/PermissionController.php` (index con grupos) | ✅ |
| Vistas | `admin/roles/{index,create,edit}.blade.php`, `admin/permisos/index.blade.php` | ✅ |
| JS | `public/js/pages/roles.js`, `public/js/pages/permisos.js` (DataTable serverSide + search manual + checkboxes) | ✅ |
| Seeder | `PermissionsDemoSeeder` — Super-Admin ahora sincroniza TODOS los permisos (`syncPermissions`); se ejecutó: Super-Admin 60, Veterinario 19, Recepcionista 14 | ✅ |

### Rutas
- Web: `admin.roles.{index,create,edit}`, `admin.permisos.index`
- API: `admin.api.roles.{index,store,update,destroy}`, `admin.api.permisos.index`

### Decisiones técnicas
| Decisión | Contexto |
|----------|----------|
| Los permisos del seeder se agrupan vía `PermissionCatalog` (16 módulos con etiquetas en español); permisos desconocidos caen en "Otros" | UI profesional de checkboxes agrupados sin hardcodear el listado en la vista |
| `Create/UpdateRoleAction` crean el rol con `guard_name=api` y `syncPermissions` solo de permisos del guard api + `forgetCachedPermissions` | Consistencia con el seeder y el guard por defecto `api` |
| `DeleteRoleAction` lanza 422: no se puede eliminar `Super-Admin` ni un rol con usuarios asignados (cuenta en `model_has_roles`) | Evita lockout y pérdida de datos; mensaje claro vía SweetAlert2 (capa ajax.js) |
| Listado de permisos de solo lectura (no hay CRUD de permisos) | Acordado en brainstorming; los permisos se crean en el seeder |
| `PermissionsDemoSeeder`: Super-Admin recibía 0 permisos (el comentario referenciaba `Gate::before`, que no existe en el proyecto) → se asignan todos con `syncPermissions` | Dato incoherente detectado al construir el módulo; se corrige el origen (seeder) |

### Sidebar
- `resources/views/layouts/app.blade.php`: entrada desplegable **"Roles y Permisos"** (`ri-shield-keyhole-line`) en Configuración, con sub-items **Roles** (`admin.roles.index`) y **Permisos** (`admin.permisos.index`).

---

## Rutas registradas

### `routes/web.php` (Admin)
| Ruta | Controlador | Método | Nombre | Fase |
|------|-------------|--------|--------|------|
| `admin/roles` | Admin\RoleController | index | admin.roles.index | Roles y Permisos |
| `admin/roles/create` | Admin\RoleController | create | admin.roles.create | Roles y Permisos |
| `admin/roles/{role}/edit` | Admin\RoleController | edit | admin.roles.edit | Roles y Permisos |
| `admin/permisos` | Admin\PermissionController | index | admin.permisos.index | Roles y Permisos |
| `admin/branches` | Admin\BranchController | index | admin.branches.index | 2 |
| `admin/branches/create` | Admin\BranchController | create | admin.branches.create | 2 |
| `admin/branches/{branch}/edit` | Admin\BranchController | edit | admin.branches.edit | 2 |
| `admin/usuarios` | Admin\UserController | index | admin.usuarios.index | 2 |
| `admin/usuarios/create` | Admin\UserController | create | admin.usuarios.create | 2 |
| `admin/usuarios/{user}/edit` | Admin\UserController | edit | admin.usuarios.edit | 2 |
| `admin/veterinarios` | Admin\VeterinarioController | index | admin.veterinarios.index | 31 |
| `admin/veterinarios/create` | Admin\VeterinarioController | create | admin.veterinarios.create | 31 |
| `admin/veterinarios/{user}/edit` | Admin\VeterinarioController | edit | admin.veterinarios.edit | 31 |
| `admin/species` | Admin\SpeciesController | index | admin.species.index | 3 || `admin/species/create` | Admin\SpeciesController | create | admin.species.create | 3 |
| `admin/species/{species}/edit` | Admin\SpeciesController | edit | admin.species.edit | 3 |
| `admin/breeds` | Admin\BreedController | index | admin.breeds.index | 3 |
| `admin/breeds/create` | Admin\BreedController | create | admin.breeds.create | 3 |
| `admin/breeds/{breed}/edit` | Admin\BreedController | edit | admin.breeds.edit | 3 |
| `admin/services` | Admin\ServiceController | index | admin.services.index | 3 |
| `admin/services/create` | Admin\ServiceController | create | admin.services.create | 3 |
| `admin/services/{service}/edit` | Admin\ServiceController | edit | admin.services.edit | 3 |
| `admin/vaccine-types` | Admin\VaccineTypeController | index | admin.vaccine-types.index | 3 |
| `admin/vaccine-types/create` | Admin\VaccineTypeController | create | admin.vaccine-types.create | 3 |
| `admin/vaccine-types/{vaccine_type}/edit` | Admin\VaccineTypeController | edit | admin.vaccine-types.edit | 3 |
| `admin/medicines` | Admin\MedicineController | index | admin.medicines.index | 3 |
| `admin/medicines/create` | Admin\MedicineController | create | admin.medicines.create | 3 |
| `admin/medicines/{medicine}/edit` | Admin\MedicineController | edit | admin.medicines.edit | 3 |
| `admin/owners` | Admin\OwnerController | index | admin.owners.index | 4 |
| `admin/owners/create` | Admin\OwnerController | create | admin.owners.create | 4 |
| `admin/owners/{owner}/edit` | Admin\OwnerController | edit | admin.owners.edit | 4 |
| `admin/owners/{owner}` | Admin\OwnerController | show | admin.owners.show | 4 |
| `admin/pacientes` | Admin\PacienteController | index | admin.pacientes.index | 4 |
| `admin/pacientes/create` | Admin\PacienteController | create | admin.pacientes.create | 4 |
| `admin/pacientes/{paciente}/edit` | Admin\PacienteController | edit | admin.pacientes.edit | 4 |
| `admin/pacientes/{paciente}` | Admin\PacienteController | show | admin.pacientes.show | 4 |
| `admin/citas` | Admin\CitaController | index | admin.citas.index | 5 |
| `admin/citas/create` | Admin\CitaController | create | admin.citas.create | 5 |
| `admin/citas/{cita}/edit` | Admin\CitaController | edit | admin.citas.edit | 5 |
| `admin/citas/calendario` | Admin\CitaController | calendar | admin.citas.calendar | 5 |
| `admin/medical-records` | Admin\MedicalRecordController | index | admin.medical-records.index | 6 |
| `admin/medical-records/create` | Admin\MedicalRecordController | create | admin.medical-records.create | 6 |
| `admin/medical-records/{record}` | Admin\MedicalRecordController | show | admin.medical-records.show | 6 |
| `admin/invoices` | Admin\InvoiceController | index | admin.invoices.index | 7 |
| `admin/invoices/create` | Admin\InvoiceController | create | admin.invoices.create | 7 |
| `admin/invoices/{invoice}` | Admin\InvoiceController | show | admin.invoices.show | 7 |
| `admin/reminders` | Admin\ReminderController | index | admin.reminders.index | 8 |

### `routes/api.php` (Api/Admin)
| Ruta | Controlador | Método | Nombre | Fase |
|------|-------------|--------|--------|------|
| `api/admin/branches` | Api\Admin\BranchController | CRUD | admin.api.branches.* | 2 |
| `api/admin/users` | Api\Admin\UserController | index/store/update/destroy | admin.api.users.* | 2 |
| `api/admin/users/{user}/toggle-status` (PATCH) | Api\Admin\UserController | toggleStatus | admin.api.users.toggle-status | 2 |
| `api/admin/veterinarios` | Api\Admin\VeterinarioController | index/store/update/destroy (rol forzado `Veterinario`) | admin.api.veterinarios.* (`parameters(["veterinarios" => "user"])`) | 31 |
| `api/admin/veterinarios/{user}/toggle-status` (PATCH) | Api\Admin\VeterinarioController | toggleStatus | admin.api.veterinarios.toggle-status | 31 |
| `api/admin/species` | Api\Admin\SpeciesController | CRUD | admin.api.species.* | 3 |
| `api/admin/breeds` | Api\Admin\BreedController | CRUD | admin.api.breeds.* | 3 |
| `api/admin/services` | Api\Admin\ServiceController | CRUD | admin.api.services.* | 3 |
| `api/admin/vaccine-types` | Api\Admin\VaccineTypeController | CRUD | admin.api.vaccine-types.* | 3 |
| `api/admin/medicines` | Api\Admin\MedicineController | CRUD | admin.api.medicines.* | 3 |
| `api/admin/owners` | Api\Admin\OwnerController | CRUD | admin.api.owners.* | 4 |
| `api/admin/pacientes` | Api\Admin\PacienteController | CRUD + ficha | admin.api.pacientes.* | 4 |
| `api/admin/pacientes/{p}/records/vacunas/cirugias` | Api\Admin\PacienteController | records/vacunas/cirugias | admin.api.pacientes.{records,vacunas,cirugias} | 4 |
| `api/admin/citas` | Api\Admin\CitaController | index/store/update/destroy | admin.api.citas.* | 5 |
| `api/admin/citas/{cita}/estado` (PATCH) | Api\Admin\CitaController | cambiarEstado | admin.api.citas.estado | 5 |
| `api/admin/vacunas` | Api\Admin\VacunaController | CRUD | admin.api.vacunas.* | 6 |
| `api/admin/cirugias` | Api\Admin\CirugiaController | CRUD + estado | admin.api.cirugias.* | 6 |
| `api/admin/medical-records` | Api\Admin\MedicalRecordController | CRUD | admin.api.medical-records.* | 6 |
| `api/admin/medical-records/{record}/adjuntos` | Api\Admin\MedicalRecordAttachmentController | store | admin.api.medical-records.adjuntos.store | 6 |
| `api/admin/adjuntos/{attachment}` | Api\Admin\MedicalRecordAttachmentController | destroy | admin.api.adjuntos.destroy | 6 |
| `api/admin/invoices` | Api\Admin\InvoiceController | index/store/show/update | admin.api.invoices.* | 7 |
| `api/admin/invoices/{invoice}/anular` (PATCH) | Api\Admin\InvoiceController | anular | admin.api.invoices.anular | 7 |
| `api/admin/invoices/{invoice}/payments` (POST) | Api\Admin\PaymentController | store | admin.api.invoices.payments.store | 7 |
| `api/admin/payments/{payment}/anular` (PATCH) | Api\Admin\PaymentController | anular | admin.api.payments.anular | 7 |
| `api/admin/reminders` | Api\Admin\ReminderController | index | admin.api.reminders.index | 8 |
| `api/admin/reminders/{reminder}/estado` (PATCH) | Api\Admin\ReminderController | cambiarEstado | admin.api.reminders.estado | 8 |
| `api/admin/roles` | Api\Admin\RoleController | index/store/update/destroy | admin.api.roles.* | Roles y Permisos |
| `api/admin/permisos` | Api\Admin\PermissionController | index | admin.api.permisos.index | Roles y Permisos |

---

## Actions registradas
| Action | Módulo | Transacción | Fase |
|--------|--------|-------------|------|
| CreateBranchAction / UpdateBranchAction / DeleteBranchAction | Branches | No | 2 |
| CreateUserAction [TX: user + horarios] (hash + avatar + assignRole) / UpdateUserAction [TX] (hash opcional + avatar + syncRoles + sync horarios) / SincronizarHorariosAction (borra+reinserta franjas del vet) / ToggleUserStatusAction / DeleteUserAction (valida dependencias; horarios en cascada, no bloquean) | Users | Sí (create/update) | 2 |
| ListVeterinariosAction (Pipeline + role Veterinario + eager branch/horarios); Requests `Veterinarios/*` heredan de Users con `role` nullable; `Api\Admin\VeterinarioController` fuerza rol server-side y reutiliza los Actions de Users | Veterinarios | No | 31 |
| CreateSpeciesAction / UpdateSpeciesAction / DeleteSpeciesAction (valida razas, pacientes, vacunas) | Species | No | 3 |
| CreateBreedAction / UpdateBreedAction / DeleteBreedAction (valida pacientes) | Breeds | No | 3 |
| CreateServiceAction / UpdateServiceAction / DeleteServiceAction (valida citas) | Services | No | 3 |
| CreateVaccineTypeAction / UpdateVaccineTypeAction / DeleteVaccineTypeAction (valida vacunas) | VaccineTypes | No | 3 |
| CreateMedicineAction / UpdateMedicineAction / DeleteMedicineAction (valida prescripciones) | Medicines | No | 3 |
| CreateOwnerAction / UpdateOwnerAction / DeleteOwnerAction (valida mascotas) | Owners | No | 4 |
| CreatePacienteAction (sube foto) / UpdatePacienteAction (sube foto) / DeletePacienteAction (borra foto, valida citas) | Pacientes | No | 4 |
| CreateScheduleAction / UpdateScheduleAction / DeleteScheduleAction | VeterinarianSchedules | — | ❌ Eliminadas 2026-08-24 (Prompt #30) |
| CreateCitaAction (asigna created_by_user_id) / UpdateCitaAction / CambiarEstadoCitaAction / DeleteCitaAction (valida historial) | Citas | No | 5 |
| CreateVacunaAction (vacuna + medical_record + reminder) / UpdateVacunaAction / DeleteVacunaAction (valida medical_records) | Vacunas | Sí | 6 |
| ValidarDisponibilidadVacuna / ObtenerDisponibilidadAction (agenda unificada citas + vacunas) | Vacunas / Citas | No | 6 |
| CreateCirugiaAction (surgiere + medical_record) / UpdateCirugiaAction / CambiarEstadoCirugiaAction / DeleteCirugiaAction (valida medical_records) | Cirugias | Sí | 6 |
| CreateMedicalRecordAction (record + prescriptions + vital_signs) / UpdateMedicalRecordAction / DeleteMedicalRecordAction | MedicalRecords | Sí | 6 |
| UploadAttachmentAction (ImageUploader) / DeleteAttachmentAction | MedicalRecordAttachments | No | 6 |
| GenerarInvoiceAction (invoice + primer payment) | Facturacion | Sí | 7 |
| AnularInvoiceAction (bloquea si hay pagos pagados) | Facturacion | Sí | 7 |
| RegistrarPagoAction (payment + recalcula invoice) | Facturacion | Sí | 7 |
| AnularPagoAction (anula pago + recalcula invoice) | Facturacion | Sí | 7 |
| List{Modulo}Action (17: Branches, Species, Breeds, Services, VaccineTypes, Medicines, Owners, Pacientes, Users, VeterinarianSchedules, Citas, Vacunas, Cirugias, MedicalRecords, Invoices, Payments, Reminders) — único dueño de armar la query con Pipeline nativo, aplica búsqueda/orden + `->paginate()` | todos los módulos | No | 9 |
| Horario semanal del veterinario integrado en `admin/usuarios/{create,edit}`: el form envía un solo campo JSON `schedules` (evita arrays anidados sobre multipart+PUT); `CreateUserAction`/`UpdateUserAction` en TX sincronizan vía `SincronizarHorariosAction`; si el rol deja de ser `Veterinario` se borran sus horarios (un recepcionista no debe aparecer en la disponibilidad); módulo standalone `veterinarian-schedules` eliminado completo (se conservan modelo, seeder y cascada de DeleteUserAction) | Users / VeterinarianSchedules | Sí | 2026-08-24 (Prompt #30) |
| Módulo dedicado **Veterinarios** (`admin/veterinarios`, index DataTable solo vets con columna de horario): el horario se define como matriz checkbox Lun-Sáb × 07:00-18:30; casillas contiguas se fusionan en franjas al guardar (necesario para citas que cruzan dos bloques); si la precarga supera las 18:30 la matriz extiende filas de 30 min sin perder datos; Personal vuelve a ser genérico; `DeleteUserAction` ya no bloquea por horarios | Veterinarios / Users | Sí | 2026-08-24 (Prompt #31) |
| El `php artisan serve` local corre con `opcache.enable_cli=On` y sirve código viejo tras editar PHP/Blade (síntomas: ruta nueva 404/param viejo, HTML de vista anterior pese a `view:cache`). Solución en este entorno: `php artisan route:clear` y/o **reiniciar el proceso serve** | Servidor dev | Sí | 2026-08-24 |
| CambiarEstadoReminderAction (pendiente → enviado/cancelado/pendiente) | Reminders | No | 8 |
| CreateRoleAction (rol guard api + syncPermissions) / UpdateRoleAction / DeleteRoleAction (bloquea Super-Admin y roles con usuarios) / ListRolesAction | Roles | No | Roles y Permisos |
| ListPermissionsAction | Permisos | No | Roles y Permisos |

---

## Filters registrados (Pipeline)
> Contrato real de Laravel `Illuminate\Pipeline\Pipeline`: `handle($query, Closure $next)` (instancia, `Request` inyectado). Se eliminó el helper `App\Filters\Pipeline`.

| Filter | Módulo | Fase |
|--------|--------|------|
| FiltrarPorVeterinario | Citas | 5 |
| FiltrarPorFecha | Citas | 5 |
| FiltrarPorEstado | Citas | 5 |
| FiltrarPorEstado / FiltrarPorRangoFecha / FiltrarPorOwner | Facturas | 7 |
| FiltrarPorSpecies (`species_id`) | Breeds | 4 |
| FiltrarPorPet (`pet_id`) | MedicalRecords | 6 |
| FiltrarPorBusqueda (genérico, columnas por constructor) | transversal (todos los listados) | 9 |
| OrdenarPor (genérico, mapa ordenable + default por constructor) | transversal (todos los listados) | 9 |
| FiltrarPorEstado (`status`) | Payments | 9 |
| FiltrarPorBusquedaVacunas / FiltrarPorEspecieVacuna / FiltrarPorVeterinarioVacuna / FiltrarPorEstadoPagoVacuna / FiltrarPorFechaVacuna | Vacunas | 6 |
| FiltrarPorBusquedaReminders / FiltrarPorTipoReminder / FiltrarPorPetReminder / FiltrarPorEstadoReminder | Reminders | 8 |
| FiltrarPorGrupoPermiso (`grupo` del catálogo, "Otros" = fuera del catálogo) | Permisos | Roles y Permisos |

---

## Resources registrados
| Resource | Módulo | Fase |
|----------|--------|------|
| BranchResource | Branches | 2 |
| UserResource | Users | 2 |
| SpeciesResource | Species | 3 |
| BreedResource | Breeds | 3 |
| ServiceResource | Services | 3 |
| VaccineTypeResource | VaccineTypes | 3 |
| MedicineResource | Medicines | 3 |
| OwnerResource | Owners | 4 |
| PacienteResource | Pacientes | 4 |
| VeterinarianScheduleResource | VeterinarianSchedules | 5 → ❌ Eliminado 2026-08-24 (Prompt #30) |
| VeterinarioResource (extiende UserResource + `horarios`) | Veterinarios | 31 |
| CitaResource | Citas | 5 |
| VacunaResource / CirugiaResource / MedicalRecordResource / PrescriptionResource / VitalSignResource / MedicalRecordAttachmentResource | Módulo clínico | 6 |
| InvoiceResource / PaymentResource | Facturación | 7 |
| ReminderResource | Reminders | 8 |
| RoleResource / PermissionResource | Roles y Permisos | Roles y Permisos |
| RoleResource / PermissionResource | Roles y Permisos | Roles y Permisos |

---

## Vistas Blade
| Vista | Tipo | Módulo | Fase |
|-------|------|--------|------|
| admin/branches/index.blade.php | index + DataTable | Branches | 2 |
| admin/branches/create.blade.php | create | Branches | 2 |
| admin/branches/edit.blade.php | edit | Branches | 2 |
| admin/usuarios/index.blade.php | index + DataTable | Users | 2 |
| admin/usuarios/create.blade.php | create | Users | 2 |
| admin/usuarios/edit.blade.php | edit | Users | 2 |
| admin/species/index.blade.php | index + DataTable | Species | 3 |
| admin/species/create.blade.php | create | Species | 3 |
| admin/species/edit.blade.php | edit | Species | 3 |
| admin/breeds/index.blade.php | index + DataTable | Breeds | 3 |
| admin/breeds/create.blade.php | create | Breeds | 3 |
| admin/breeds/edit.blade.php | edit | Breeds | 3 |
| admin/services/index.blade.php | index + DataTable | Services | 3 |
| admin/services/create.blade.php | create | Services | 3 |
| admin/services/edit.blade.php | edit | Services | 3 |
| admin/vaccine-types/index.blade.php | index + DataTable | VaccineTypes | 3 |
| admin/vaccine-types/create.blade.php | create | VaccineTypes | 3 |
| admin/vaccine-types/edit.blade.php | edit | VaccineTypes | 3 |
| admin/medicines/index.blade.php | index + DataTable | Medicines | 3 |
| admin/medicines/create.blade.php | create | Medicines | 3 |
| admin/medicines/edit.blade.php | edit | Medicines | 3 |
| admin/owners/index.blade.php | index + DataTable | Owners | 4 |
| admin/owners/create.blade.php | create | Owners | 4 |
| admin/owners/edit.blade.php | edit | Owners | 4 |
| admin/owners/show.blade.php | show (datos + mascotas) | Owners | 4 |
| admin/pacientes/index.blade.php | index + DataTable | Pacientes | 4 |
| admin/pacientes/create.blade.php | create (breed dinámico) | Pacientes | 4 |
| admin/pacientes/edit.blade.php | edit | Pacientes | 4 |
| admin/pacientes/show.blade.php | show (ficha con tabs) | Pacientes | 4 |
| admin/veterinarian-schedules/{index,create,edit}.blade.php | index + DataTable / create / edit | VeterinarianSchedules | ❌ Eliminadas 2026-08-24 (Prompt #30) — reemplazadas por el módulo Veterinarios (Prompt #31) |
| admin/veterinarios/index.blade.php | index + DataTable (solo vets, columna "Horario de atención") | Veterinarios | 31 |
| admin/veterinarios/create.blade.php | create + matriz checkbox de horario | Veterinarios | 31 |
| admin/veterinarios/edit.blade.php | edit + matriz precargada (`veterinariosHorariosInit`) | Veterinarios | 31 |
| admin/citas/index.blade.php | index + DataTable con filtros | Citas | 5 |
| admin/citas/create.blade.php | create | Citas | 5 |
| admin/citas/edit.blade.php | edit | Citas | 5 |
| admin/citas/calendar.blade.php | calendar (FullCalendar global, drawer derecho, leyenda vets) | Citas | 5 |
| admin/vacunas/{index,create,edit}.blade.php | index + DataTable con filtros / create / edit (disponibilidad + pago) | Vacunas | 6 |
| admin/cirugias/{index,create,edit}.blade.php | index + DataTable / create / edit | Cirugias | 6 |
| admin/medical-records/{index,create,show}.blade.php | index + DataTable / create (recetas dinámicas) / show (adjuntos) | MedicalRecords | 6 |
| admin/facturacion/invoices/{index,create,show}.blade.php | index + DataTable con filtros / create (servicio dinámico) / show (pagos) | Invoices | 7 |
| admin/reminders/index.blade.php | index + DataTable con filtros (search, mascota, tipo, estado) | Reminders | 8 |
| admin/roles/{index,create,edit}.blade.php | index + DataTable con search / create (checkboxes agrupados) / edit (permisos marcados + aviso Super-Admin) | Roles y Permisos | Roles y Permisos |
| admin/permisos/index.blade.php | index + DataTable read-only con search y filtro por módulo | Roles y Permisos | Roles y Permisos |
| admin/roles/{index,create,edit}.blade.php | index + DataTable con search / create / edit (checkboxes de permisos agrupados por módulo) | Roles | Roles y Permisos |
| admin/permisos/index.blade.php | index + DataTable read-only (search + filtro por módulo) | Permisos | Roles y Permisos |

---

## Decisiones técnicas

| Decisión | Contexto | Fase |
|----------|----------|------|
| Columna `is_active` agregada a `users` (migración propia) para el toggle activar/desactivar | No existía campo de estado en el esquema original | 2 |
| Rutas API con prefijo de nombre `admin.api.*` para evitar colisión con rutas web `admin.*` | `route('admin.X.index')` debe apuntar a la URL web | 2 |
| `app/Support/ImageUploader.php`: sube a Cloudinary (REST firmado) si hay credenciales en `.env`, si no a disco local `storage/app/public/uploads/{folder}` | No hay SDK de Cloudinary instalado; fallback que no rompe | 2 |
| Views de Users bajo `admin/usuarios/`, rutas web `admin.usuarios.*`; API bajo `admin/users` | Nomenclatura del plan | 2 |
| Rutas de página JS guardan por existencia de elemento (`#table-X` / formulario) para reutilizar el mismo archivo en index/create/edit | Un solo archivo JS por módulo | 3 |
| El prefijo de nombre `admin.api.` del grupo en `routes/api.php` también aplica a rutas explícitas `->name()` (no repetir el prefijo) | Corrige nombres duplicados `admin.api.admin.api.*` en ficha de pacientes | 4 |
| Tabs de la ficha de pacientes (`paciente-ficha.js`) cargan vía AJAX endpoints `records`, `vacunas`, `cirugias` del Api\PacienteController | Evita depender de recursos de fases posteriores; retorna JSON plano | 4 |
| Select de razas dinámico por especie vía `GET api/admin/breeds?species_id=X` (endpoint index de Breeds ahora filtra) | Requisito del plan para create/edit de pacientes | 4 |
| Pipeline de filtros: `Illuminate\Pipeline\Pipeline` real (`app(Pipeline::class)->send(...)->through([...])->thenReturn()`) + Filters con contrato `handle($query, Closure $next)` (instancias con `Request` inyectado) | Fix #5: sustituye el helper propio `App\Filters\Pipeline` y la firma estática inventada por el patrón nativo de Laravel | 9 |
| Búsqueda global y ordenamiento extraídos de los `index()` a Filters **genéricos** `Shared/FiltrarPorBusqueda` + `Shared/OrdenarPor` (configurables por constructor), y cada listado vive en un `List{Modulo}Action` (único dueño de armar la query); el `index()` del controller queda delgado (inyecta la Action + envelope) | Fix #6: los 16 `index()` de `Api/Admin` repetían lógica cruda de `where/orWhere` de búsqueda y mapeo manual de columnas ordenables; el patrón variaba poco entre módulos → se hizo genérico en vez de 32 Filters por módulo. Los Filters de dominio (Species/Pet/Citas/Facturas) se conservan por módulo | 9 |
| Validación de citas cruza `veterinarian_schedules` (día de semana + rango horario activo) y detecta cruce con otra cita del mismo vet/fecha/hora | Evita agendar fuera de horario o duplicado | 5 |
| Calendario de citas con FullCalendar v6 (CDN global en layout) + endpoint dedicado `ObtenerCitasCalendarioAction`/`CitaCalendarioResource` (todas las citas, color por veterinario, `extendedProps` con mascota/dueño/vet/costo/notas); evento click abre Drawer (Offcanvas derecho) con info + estado editable (PATCH estado); CSS propio + leyenda de veterinarios + tooltip | Vista tipo calendario del plan, enriquecida con info y edición de estado inline | 5 |
| Facturas polimórficas (`invoiceable_type`/`invoiceable_id`) a Cita/Vacuna/Surgiere con etiqueta `invoiceableLabel()` en el Resource | Esquema original de la tabla `invoices` | 7 |
| Status de invoice recalculado desde los pagos (pagado/parcial/pendiente) y por anulación (anulado) vía Actions [TX] | Consistencia del saldo con los pagos | 7 |
| Registro de pagos anidado a factura (POST invoices/{invoice}/payments); anulación de pago con PATCH /payments/{payment}/anular | El plan 7.2 integra el pago dentro de la vista show de factura, sin listado propio | 7 |
| Vacunas replican el patrón de Citas: `vaccination_time` (TIME) nuevo en `vacunas`, selector fecha → vets → horas libres/ocupadas (agenda unificada citas+vacunas en `ObtenerDisponibilidadAction`), bloque Pago (método/adelanto obligatorios) con invoice polimórfica en la misma TX del store/update, y creación inline de un nuevo `VaccineType` desde el formulario (replica `new_service` de Citas: opción `__nuevo__` + bloque con nombre/precio/especie) | Módulo clínico rediseñado con disponibilidad real y pago desde el formulario | 6 |
| Pipeline de filtros de facturas: `FiltrarPorEstado`, `FiltrarPorRangoFecha` (desde/hasta), `FiltrarPorOwner` | Consistencia con el pipeline de Citas | 7 |
| El seeder de facturas factura solo servicios `completada` (citas/cirugías) y todas las vacunas con montos base fijos por tipo | Datos demo coherentes con el estado real de los servicios | 7 |
| Los `index()` de `Api/Admin/*` que alimentan DataTable usan **`->paginate()` nativo** y devuelven un envelope agnóstico `{success, data, pagination}`; **no** devuelven el contrato DataTables. El plugin DataTables.net (CDN, ya server-side) recibe `draw/recordsTotal/recordsFiltered` reconstruidos por una función `ajax` en el JS que traduce `{success,data,pagination}`; el JS reenvía `search`/`sort_by`/`sort_dir` para conservar búsqueda global y ordenamiento por columna. Solo `index()` cambia; `store/update/destroy/show` conservan `{status,message,data,errors}` | Fix #5: elimina la clase custom `App\Filters\DataTables` (la librería JS ya incluye server-side, no hace falta wrapper PHP) | 9 |
| Manejo de errores 422 en la capa AJAX compartida `public/js/config/ajax.js` como **single source of truth**: todo 422 muestra SIEMPRE SweetAlert2 con el mensaje real del backend (`response?.message` fallback al primer `errors`, fallback por método) y luego hace `reject()` para que la página pinte inline si quiere; título según método (`DELETE` = "No se pudo eliminar", resto = "No se pudo guardar"). Los `.catch(422)` redundantes con `Swal.fire` de los deletes se eliminaron (el Swal global ya los cubre) | Fix #7: el 422 llegaba al frontend pero quedaba invisible en Citas porque el campo `appointment_time` es `hidden` y la capa AJAX solo hacía `reject()` silencioso; se unifica para todos los módulos del admin (no solo Citas) | 5 |
| La búsqueda de Reminders cubre `message` + `remindable_type` + `whereHas('paciente', name)`; filtros dedicados `tipo`/`pet_id`/`status`; orden por defecto `remind_at` asc | Cumple la Fase 8: listado de reminders pendientes con relación a Paciente y filtrar por tipo (cita/vacuna/cirugia) | 8 |
| `ReminderResource::typeLabel()` reutiliza el match de InvoiceResource (`surgiere`→Cirugía por compatibilidad); seeder demo idempotente inserta directo (no pasa por Actions) | Dato demo para el listado (tabla `reminders` estaba vacía); los reminders reales solo los crean las transacciones clínicas | 8 |

---

## Fixes aplicados

| Fix | Archivo | Fecha | Referencia |
|-----|---------|-------|------------|
| _Se irán documentando_ | | | |
| Sidebar UI: tamaño de fuente + menús redundantes | `resources/views/layouts/app.blade.php` | 2026-08-06 | `fixes/2026-08-06-sidebar-ui-inconsistencia.md` |
| Error en Medical Records index (`$title` faltante) + rutas Invoices/Pagos inconsistentes (tabs + `admin.api.payments.index`) | `MedicalRecordController`, `PaymentController`, `PaymentResource`, `routes/api.php`, `invoices/index.blade.php`, `invoices.js`, `layouts/app.blade.php` | 2026-08-06 | `fixes/2026-08-06-medical-records-error-e-invoices-rutas.md` |
| Pipeline de filtros extendido + paginación server-side `{data, meta}` en los 16 listados admin (DataTable serverSide) + auditoría de reglas de negocio | `app/Filters/DataTables.php`, `FiltrarPorSpecies`, `FiltrarPorPet`, 16 `Api/Admin/*Controller`, 16 `js/pages/*.js` | 2026-08-06 | `fixes/2026-08-06-pipeline-filtros-y-paginacion-server-side.md` |
| Migración a DataTables server-side **real** (contrato oficial `{draw, recordsTotal, recordsFiltered, data}`) en los 16 listados; corrección de URLs de DataTable a `/api/admin/{modulo}` (apuntaban a la ruta web HTML) y de llamadas crudas `/admin-api/...` inválidas | `app/Filters/DataTables.php`, 16 `Api/Admin/*`, 15 `js/pages/*.js` | 2026-08-06 | `fixes/2026-08-06-datatables-serverside.md` |
| Eliminadas las clases custom `App\Filters\Pipeline` y `App\Filters\DataTables`; Filters migrados al contrato real de Laravel `handle($query, Closure $next)` con `Illuminate\Pipeline\Pipeline`; los 16 `index()` usan `->paginate()` nativo con envelope `{success, data, pagination}` y el JS traduce con `ajax`-función (conserva búsqueda global y ordenamiento) | 8 Filters, 16 `Api/Admin/*Controller`, 17 `js/pages/*.js` | 2026-08-06 | `fixes/2026-08-06-pipeline-nativo-y-datatables-response.md` |
| Lógica de búsqueda/orden extraída de los `index()` a Filters `Shared/FiltrarPorBusqueda` + `Shared/OrdenarPor` (genéricos) y a 16 `List*Action` (único dueño de armar la query, Pipeline + `paginate()`); `index()` de controllers delgados; extraído `Pagos/FiltrarPorEstado`; limpiada lógica muerta de `Admin/MedicalRecordController@index` (el blade es DataTable server-side, `$records` no se usaba) | `app/Filters/Shared/*`, `app/Filters/Pagos/*`, 16 `app/Actions/*/List*Action.php`, 16 `Api/Admin/*Controller`, 1 `Admin/*Controller` | 2026-08-06 | `fixes/2026-08-06-logica-suelta-controllers.md` |
| Citas duplicadas en el mismo horario: diagnóstico confirmó que NO se duplica en BD (backend rechaza con 422 en Request y Action); causa raíz = bug de UI (422 silencioso en ajax.js + `appointment_time` hidden invisible). Fix: 422 muestra SIEMPRE Swal con el mensaje real backend en la capa AJAX compartida (título por método), se quitaron los Swal redundantes de 11 deletes | `public/js/config/ajax.js`, 11 `js/pages/*.js` (breeds, vaccine-types, usuarios, vacunas, citas, cirugias, medicines, pacientes, services, species, owners) | 2026-08-10 | `fixes/2026-08-06-citas-duplicadas.md` |
| UI del módulo Vacunas replicando Citas: filtros (mascota/especie/veterinario/estado de pago/fecha), disponibilidad de veterinarios (fecha→vets→horas, agenda unificada con citas), sección Pago obligatoria (invoice + payment en TX); + `vaccination_time` y `base_price` en BD (migración + seeders) + filtros de Vacunas + CRUD vaccine-types con precio. Se corrigió además el bug preexistente de `UpdateVaccineTypeRequest` (`{$this->vaccineType->id}` era null → la regla unique usaba `route('vaccine_type')`) | `app/Actions/Vacunas/*`, `app/Filters/Vacunas/*`, `app/Http/Requests/Vacunas/*`, `VacunaResource`, `Api/Admin/VacunaController`, `Admin/VacunaController`, `routes/api.php`, `admin/vacunas/{index,create,edit}.blade.php`, `public/js/pages/vacunas.js`, `app/Http/Requests/VaccineTypes/UpdateVaccineTypeRequest.php`, `database/migrations/2026_08_11_000001_add_time_and_price_to_vacunas_and_vaccine_types.php`, `database/seeders/{VaccineTypeSeeder,VacunaSeeder}.php`, `db_sistema_veterinaria.sql` | 2026-08-11 | `fixes/2026-08-11-vacunas-ui.md` |
| Update de Vacunas roto: `UpdateVacunaRequest` con `after_or_equal:today` impedía editar las 6 vacunas del seed (todas con fecha pasada → 422 siempre). Fix: la regla de `vaccination_date` en update es una Closure que permite la fecha original pasada; el input `min` de la vista edit usa la fecha original; la vacuna de Kiara movida de domingo (sin horario) a viernes; scripts reordenados (datos de init antes del IIFE de vacunas.js para que preview/preselección apliquen al cargar) | `app/Http/Requests/Vacunas/UpdateVacunaRequest.php`, `resources/views/admin/vacunas/edit.blade.php`, `database/seeders/VacunaSeeder.php` | 2026-08-11 | `fixes/2026-08-11-vacunas-ui.md` |
| Módulo Reminders Fase 8 completo: listado (search texto+mascota+tipo, filtros mascota/tipo/estado, DataTable serverSide orden `remind_at` asc), cambiar estado (enviado/cancelado/volver pendiente vía PATCH), sidebar "Recordatorios", seeder demo idempotente (11 reminders cita/vacuna/cirugia) | `app/Http/Requests/Reminders/*`, `app/Actions/Reminders/*`, `app/Filters/Reminders/*`, `ReminderResource`, `Api/Admin/ReminderController`, `Admin/ReminderController`, `routes/{web,api}.php`, `admin/reminders/index.blade.php`, `public/js/pages/reminders.js`, `database/seeders/ReminderSeeder.php`, `layouts/app.blade.php`, `DatabaseSeeder.php` | 2026-08-11 | `fixes/2026-08-11-reminders-module.md` |
