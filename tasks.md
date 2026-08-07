# Tasks — Sistema Veterinaria

> Estado: `[ ]` pendiente · `[/]` en progreso · `[x]` completada

---

## Fase 0 — Infraestructura transversal

- [x] 0.1 Capa AJAX (`public/js/config/ajax.js`)
- [x] 0.2 Helpers JS de formulario (`public/js/helpers/form-helpers.js`)
- [x] 0.3 Verificar/completar modelos Eloquent (20 modelos, $fillable, $casts, relaciones)
- [x] 0.4 Layout base — cargar dependencias faltantes (jQuery, DataTables, SweetAlert2)

---

## Fase 2 — Sedes y personal

### 2.1 Branches (Sucursales)
- [x] 2.1.1 Form Request: `StoreBranchRequest`, `UpdateBranchRequest`
- [x] 2.1.2 Actions: `CreateBranchAction`, `UpdateBranchAction`, `DeleteBranchAction`
- [x] 2.1.3 Controller Api: `Api/Admin/BranchController`
- [x] 2.1.4 Rutas API: `admin/branches`
- [x] 2.1.5 Resource: `BranchResource`
- [x] 2.1.6 Controller Admin: `Admin/BranchController`
- [x] 2.1.7 Rutas Web: `admin/branches`
- [x] 2.1.8 Vista index: `admin/branches/index.blade.php`
- [x] 2.1.9 Vista crear: `admin/branches/create.blade.php`
- [x] 2.1.10 Vista editar: `admin/branches/edit.blade.php`
- [x] 2.1.11 JS de página: `branches.js`
- [x] 2.1.12 Seeder: `BranchSeeder`

### 2.2 Users (Personal)
- [x] 2.2.1 Form Request: `StoreUserRequest`, `UpdateUserRequest`
- [x] 2.2.2 Actions: `CreateUserAction`, `UpdateUserAction`, `ToggleUserStatusAction`
- [x] 2.2.3 Controller Api: `Api/Admin/UserController`
- [x] 2.2.4 Rutas API: `admin/users`
- [x] 2.2.5 Resource: `UserResource`
- [x] 2.2.6 Controller Admin: `Admin/UserController`
- [x] 2.2.7 Rutas Web: `admin/users`
- [x] 2.2.8 Vista index: `admin/usuarios/index.blade.php`
- [x] 2.2.9 Vista crear: `admin/usuarios/create.blade.php`
- [x] 2.2.10 Vista editar: `admin/usuarios/edit.blade.php`
- [x] 2.2.11 JS de página: `usuarios.js`
- [x] 2.2.12 Seeder: `UserSeeder`

---

## Fase 3 — Catálogos base

### 3.1 Species (Especies)
- [x] 3.1.1 Form Request: `StoreSpeciesRequest`, `UpdateSpeciesRequest`
- [x] 3.1.2 Actions: `CreateSpeciesAction`, `UpdateSpeciesAction`, `DeleteSpeciesAction`
- [x] 3.1.3 Controller Api: `Api/Admin/SpeciesController`
- [x] 3.1.4 Rutas API: `admin/species`
- [x] 3.1.5 Resource: `SpeciesResource`
- [x] 3.1.6 Controller Admin: `Admin/SpeciesController`
- [x] 3.1.7 Rutas Web: `admin/species`
- [x] 3.1.8 Vista index: `admin/species/index.blade.php`
- [x] 3.1.9 Vista crear: `admin/species/create.blade.php`
- [x] 3.1.10 Vista editar: `admin/species/edit.blade.php`
- [x] 3.1.11 JS de página: `species.js`
- [x] 3.1.12 Seeder: `SpeciesSeeder`

### 3.2 Breeds (Razas)
- [x] 3.2.1 Form Request: `StoreBreedRequest`, `UpdateBreedRequest`
- [x] 3.2.2 Actions: `CreateBreedAction`, `UpdateBreedAction`, `DeleteBreedAction`
- [x] 3.2.3 Controller Api: `Api/Admin/BreedController`
- [x] 3.2.4 Rutas API: `admin/breeds`
- [x] 3.2.5 Resource: `BreedResource`
- [x] 3.2.6 Controller Admin: `Admin/BreedController`
- [x] 3.2.7 Rutas Web: `admin/breeds`
- [x] 3.2.8 Vista index: `admin/breeds/index.blade.php`
- [x] 3.2.9 Vista crear: `admin/breeds/create.blade.php`
- [x] 3.2.10 Vista editar: `admin/breeds/edit.blade.php`
- [x] 3.2.11 JS de página: `breeds.js`
- [x] 3.2.12 Seeder: `BreedSeeder`

### 3.3 Services (Servicios)
- [x] 3.3.1 Form Request: `StoreServiceRequest`, `UpdateServiceRequest`
- [x] 3.3.2 Actions: `CreateServiceAction`, `UpdateServiceAction`, `DeleteServiceAction`
- [x] 3.3.3 Controller Api: `Api/Admin/ServiceController`
- [x] 3.3.4 Rutas API: `admin/services`
- [x] 3.3.5 Resource: `ServiceResource`
- [x] 3.3.6 Controller Admin: `Admin/ServiceController`
- [x] 3.3.7 Rutas Web: `admin/services`
- [x] 3.3.8 Vista index: `admin/services/index.blade.php`
- [x] 3.3.9 Vista crear: `admin/services/create.blade.php`
- [x] 3.3.10 Vista editar: `admin/services/edit.blade.php`
- [x] 3.3.11 JS de página: `services.js`
- [x] 3.3.12 Seeder: `ServiceSeeder`

### 3.4 Vaccine Types (Tipos de vacuna)
- [x] 3.4.1 Form Request: `StoreVaccineTypeRequest`, `UpdateVaccineTypeRequest`
- [x] 3.4.2 Actions: `CreateVaccineTypeAction`, `UpdateVaccineTypeAction`, `DeleteVaccineTypeAction`
- [x] 3.4.3 Controller Api: `Api/Admin/VaccineTypeController`
- [x] 3.4.4 Rutas API: `admin/vaccine-types`
- [x] 3.4.5 Resource: `VaccineTypeResource`
- [x] 3.4.6 Controller Admin: `Admin/VaccineTypeController`
- [x] 3.4.7 Rutas Web: `admin/vaccine-types`
- [x] 3.4.8 Vista index: `admin/vaccine-types/index.blade.php`
- [x] 3.4.9 Vista crear: `admin/vaccine-types/create.blade.php`
- [x] 3.4.10 Vista editar: `admin/vaccine-types/edit.blade.php`
- [x] 3.4.11 JS de página: `vaccine-types.js`
- [x] 3.4.12 Seeder: `VaccineTypeSeeder`

### 3.5 Medicines (Medicamentos)
- [x] 3.5.1 Form Request: `StoreMedicineRequest`, `UpdateMedicineRequest`
- [x] 3.5.2 Actions: `CreateMedicineAction`, `UpdateMedicineAction`, `DeleteMedicineAction`
- [x] 3.5.3 Controller Api: `Api/Admin/MedicineController`
- [x] 3.5.4 Rutas API: `admin/medicines`
- [x] 3.5.5 Resource: `MedicineResource`
- [x] 3.5.6 Controller Admin: `Admin/MedicineController`
- [x] 3.5.7 Rutas Web: `admin/medicines`
- [x] 3.5.8 Vista index: `admin/medicines/index.blade.php`
- [x] 3.5.9 Vista crear: `admin/medicines/create.blade.php`
- [x] 3.5.10 Vista editar: `admin/medicines/edit.blade.php`
- [x] 3.5.11 JS de página: `medicines.js`
- [x] 3.5.12 Seeder: `MedicineSeeder`

---

## Fase 4 — Dueños y mascotas

### 4.1 Owners (Propietarios)
- [x] 4.1.1 Form Request: `StoreOwnerRequest`, `UpdateOwnerRequest`
- [x] 4.1.2 Actions: `CreateOwnerAction`, `UpdateOwnerAction`, `DeleteOwnerAction`
- [x] 4.1.3 Controller Api: `Api/Admin/OwnerController`
- [x] 4.1.4 Rutas API: `admin/owners`
- [x] 4.1.5 Resource: `OwnerResource`
- [x] 4.1.6 Controller Admin: `Admin/OwnerController`
- [x] 4.1.7 Rutas Web: `admin/owners`
- [x] 4.1.8 Vista index: `admin/owners/index.blade.php`
- [x] 4.1.9 Vista crear: `admin/owners/create.blade.php`
- [x] 4.1.10 Vista editar: `admin/owners/edit.blade.php`
- [x] 4.1.11 Vista show: `admin/owners/show.blade.php`
- [x] 4.1.12 JS de página: `owners.js`
- [x] 4.1.13 Seeder: `OwnerSeeder`

### 4.2 Pacientes (Mascotas)
- [x] 4.2.1 Form Request: `StorePacienteRequest`, `UpdatePacienteRequest`
- [x] 4.2.2 Actions: `CreatePacienteAction`, `UpdatePacienteAction`, `DeletePacienteAction`
- [x] 4.2.3 Controller Api: `Api/Admin/PacienteController`
- [x] 4.2.4 Rutas API: `admin/pacientes`
- [x] 4.2.5 Resource: `PacienteResource`
- [x] 4.2.6 Controller Admin: `Admin/PacienteController`
- [x] 4.2.7 Rutas Web: `admin/pacientes`
- [x] 4.2.8 Vista index: `admin/pacientes/index.blade.php`
- [x] 4.2.9 Vista crear: `admin/pacientes/create.blade.php`
- [x] 4.2.10 Vista editar: `admin/pacientes/edit.blade.php`
- [x] 4.2.11 Vista show (ficha): `admin/pacientes/show.blade.php`
- [x] 4.2.12 JS de página: `pacientes.js`
- [x] 4.2.13 JS de ficha: `paciente-ficha.js`
- [x] 4.2.14 Seeder: `PacienteSeeder`

---

## Fase 5 — Horario y citas

### 5.1 Veterinarian Schedules (Horarios)
- [x] 5.1.1 Form Request: `StoreScheduleRequest`, `UpdateScheduleRequest`
- [x] 5.1.2 Actions: `CreateScheduleAction`, `UpdateScheduleAction`, `DeleteScheduleAction`
- [x] 5.1.3 Controller Api: `Api/Admin/VeterinarianScheduleController`
- [x] 5.1.4 Rutas API: `admin/veterinarian-schedules`
- [x] 5.1.5 Resource: `VeterinarianScheduleResource`
- [x] 5.1.6 Controller Admin: `Admin/VeterinarianScheduleController`
- [x] 5.1.7 Rutas Web: `admin/veterinarian-schedules`
- [x] 5.1.8 Vista index: `admin/veterinarian-schedules/index.blade.php`
- [x] 5.1.9 Vista crear: `admin/veterinarian-schedules/create.blade.php`
- [x] 5.1.10 Vista editar: `admin/veterinarian-schedules/edit.blade.php`
- [x] 5.1.11 JS de página: `veterinarian-schedules.js`
- [x] 5.1.12 Seeder: `VeterinarianScheduleSeeder`

### 5.2 Citas
- [x] 5.2.1 Form Request: `StoreCitaRequest`, `UpdateCitaRequest`
- [x] 5.2.2 Actions: `CreateCitaAction`, `UpdateCitaAction`, `CambiarEstadoCitaAction`, `DeleteCitaAction`
- [x] 5.2.3 Filters: `FiltrarPorVeterinario`, `FiltrarPorFecha`, `FiltrarPorEstado`
- [x] 5.2.4 Controller Api: `Api/Admin/CitaController`
- [x] 5.2.5 Rutas API: `admin/citas`
- [x] 5.2.6 Resource: `CitaResource`
- [x] 5.2.7 Controller Admin: `Admin/CitaController`
- [x] 5.2.8 Rutas Web: `admin/citas`
- [x] 5.2.9 Vista index: `admin/citas/index.blade.php`
- [x] 5.2.10 Vista crear: `admin/citas/create.blade.php`
- [x] 5.2.11 Vista editar: `admin/citas/edit.blade.php`
- [x] 5.2.12 Vista calendario: `admin/citas/calendar.blade.php`
- [x] 5.2.13 JS de página: `citas.js`
- [x] 5.2.14 JS de calendario: `citas-calendar.js`
- [x] 5.2.15 Seeder: `CitaSeeder`

---

## Fase 6 — Módulo clínico

### 6.1 Vacunas [TX]
- [x] 6.1.1 Form Request: `StoreVacunaRequest`, `UpdateVacunaRequest`
- [x] 6.1.2 Action [TX]: `CreateVacunaAction` (vacuna + medical_record + reminder)
- [x] 6.1.3 Actions: `UpdateVacunaAction`, `DeleteVacunaAction`
- [x] 6.1.4 Controller Api: `Api/Admin/VacunaController`
- [x] 6.1.5 Rutas API: `admin/vacunas`
- [x] 6.1.6 Resource: `VacunaResource`
- [x] 6.1.7 Controller Admin: `Admin/VacunaController`
- [x] 6.1.8 Rutas Web: `admin/vacunas`
- [x] 6.1.9 Vista index: `admin/vacunas/index.blade.php`
- [x] 6.1.10 Vista crear: `admin/vacunas/create.blade.php`
- [x] 6.1.11 Vista editar: `admin/vacunas/edit.blade.php`
- [x] 6.1.12 JS de página: `vacunas.js`
- [x] 6.1.13 Seeder: `VacunaSeeder`

### 6.2 Cirugías (Surgiere) [TX]
- [x] 6.2.1 Form Request: `StoreCirugiaRequest`, `UpdateCirugiaRequest`
- [x] 6.2.2 Action [TX]: `CreateCirugiaAction` (surgiere + medical_record)
- [x] 6.2.3 Actions: `UpdateCirugiaAction`, `CambiarEstadoCirugiaAction`, `DeleteCirugiaAction`
- [x] 6.2.4 Controller Api: `Api/Admin/CirugiaController`
- [x] 6.2.5 Rutas API: `admin/cirugias`
- [x] 6.2.6 Resource: `CirugiaResource`
- [x] 6.2.7 Controller Admin: `Admin/CirugiaController`
- [x] 6.2.8 Rutas Web: `admin/cirugias`
- [x] 6.2.9 Vista index: `admin/cirugias/index.blade.php`
- [x] 6.2.10 Vista crear: `admin/cirugias/create.blade.php`
- [x] 6.2.11 Vista editar: `admin/cirugias/edit.blade.php`
- [x] 6.2.12 JS de página: `cirugias.js`
- [x] 6.2.13 Seeder: `CirugiaSeeder`

### 6.3 Medical Records (Historial médico) [TX]
- [x] 6.3.1 Form Request: `StoreMedicalRecordRequest`, `UpdateMedicalRecordRequest`
- [x] 6.3.2 Action [TX]: `CreateMedicalRecordAction` (record + prescriptions + vital_signs)
- [x] 6.3.3 Actions: `UpdateMedicalRecordAction`, `DeleteMedicalRecordAction`
- [x] 6.3.4 Controller Api: `Api/Admin/MedicalRecordController`
- [x] 6.3.5 Rutas API: `admin/medical-records`
- [x] 6.3.6 Resource: `MedicalRecordResource`
- [x] 6.3.7 Controller Admin: `Admin/MedicalRecordController`
- [x] 6.3.8 Rutas Web: `admin/medical-records`
- [x] 6.3.9 Vista index: `admin/medical-records/index.blade.php`
- [x] 6.3.10 Vista crear: `admin/medical-records/create.blade.php`
- [x] 6.3.11 Vista show: `admin/medical-records/show.blade.php`
- [x] 6.3.12 JS de página: `medical-records.js`

### 6.4 Prescriptions (Resource)
- [x] 6.4.1 Resource: `PrescriptionResource`

### 6.5 Vital Signs (Resource)
- [x] 6.5.1 Resource: `VitalSignResource`

### 6.6 Medical Record Attachments
- [x] 6.6.1 Form Request: `StoreAttachmentRequest`
- [x] 6.6.2 Actions: `UploadAttachmentAction`, `DeleteAttachmentAction`
- [x] 6.6.3 Controller Api: `Api/Admin/MedicalRecordAttachmentController`
- [x] 6.6.4 Rutas API: `admin/medical-records/{record}/attachments`
- [x] 6.6.5 Resource: `MedicalRecordAttachmentResource`
- [x] 6.6.6 JS integrado en `medical-records.js`

---

## Fase 7 — Facturación y pagos

### 7.1 Invoices (Facturas) [TX] [F]
- [x] 7.1.1 Form Request: `StoreInvoiceRequest`, `UpdateInvoiceRequest`
- [x] 7.1.2 Action [TX]: `GenerarInvoiceAction` (invoice + primer payment)
- [x] 7.1.3 Action: `AnularInvoiceAction`
- [x] 7.1.4 Filters: `FiltrarPorEstado`, `FiltrarPorRangoFecha`, `FiltrarPorOwner`
- [x] 7.1.5 Controller Api: `Api/Admin/InvoiceController`
- [x] 7.1.6 Rutas API: `admin/invoices`
- [x] 7.1.7 Resource: `InvoiceResource`
- [x] 7.1.8 Controller Admin: `Admin/InvoiceController`
- [x] 7.1.9 Rutas Web: `admin/invoices`
- [x] 7.1.10 Vista index: `admin/facturacion/invoices/index.blade.php`
- [x] 7.1.11 Vista crear: `admin/facturacion/invoices/create.blade.php`
- [x] 7.1.12 Vista show: `admin/facturacion/invoices/show.blade.php`
- [x] 7.1.13 JS de página: `invoices.js`

### 7.2 Payments (Pagos) [TX]
- [x] 7.2.1 Form Request: `StorePaymentRequest`
- [x] 7.2.2 Action [TX]: `RegistrarPagoAction` (payment + actualizar invoice)
- [x] 7.2.3 Action: `AnularPagoAction`
- [x] 7.2.4 Controller Api: `Api/Admin/PaymentController`
- [x] 7.2.5 Rutas API: `admin/invoices/{invoice}/payments`
- [x] 7.2.6 Resource: `PaymentResource`
- [x] 7.2.7 JS integrado en `invoices.js`

### 7.3 Seeders de facturación
- [x] 7.3.1 Seeder: `InvoiceSeeder`
- [x] 7.3.2 Seeder: `PaymentSeeder`

---

## Fase 8 — Recordatorios

### 8.1 Reminders
- [ ] 8.1.1 Form Request: `UpdateReminderRequest`
- [ ] 8.1.2 Action: `CambiarEstadoReminderAction`
- [ ] 8.1.3 Controller Api: `Api/Admin/ReminderController`
- [ ] 8.1.4 Rutas API: `admin/reminders`
- [ ] 8.1.5 Resource: `ReminderResource`
- [ ] 8.1.6 Controller Admin: `Admin/ReminderController`
- [ ] 8.1.7 Rutas Web: `admin/reminders`
- [ ] 8.1.8 Vista index: `admin/reminders/index.blade.php`
- [ ] 8.1.9 JS de página: `reminders.js`

---

## Tareas transversales

- [x] Actualizar `DatabaseSeeder.php` con todos los seeders en orden de dependencia (pipeline completo e idempotente)
- [x] Cablear sidebar (`app.blade.php`): reemplazar `href="#"` con rutas reales por cada fase completada
