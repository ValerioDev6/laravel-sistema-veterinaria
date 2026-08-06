# Project Map — Sistema Veterinaria

> Fuente de verdad del estado real del sistema. Se actualiza al terminar cada fase.
> Si hay discrepancia entre el código y este archivo, el código manda y el archivo se corrige.

---

## Estado general

| Fase | Estado | Fecha completada |
|------|--------|-----------------|
| 1 — Auth + RBAC + Layout Velzon | ✅ Completada (pre-existente) | — |
| 2 — Sedes y personal | ⏳ Pendiente | |
| 3 — Catálogos base | ⏳ Pendiente | |
| 4 — Dueños y mascotas | ⏳ Pendiente | |
| 5 — Horario y citas | ⏳ Pendiente | |
| 6 — Módulo clínico | ⏳ Pendiente | |
| 7 — Facturación y pagos | ⏳ Pendiente | |
| 8 — Recordatorios | ⏳ Pendiente | |
| 9 — Reportes | 🚫 Fuera de alcance | |

---

## Infraestructura existente (Fase 1)

### Auth y permisos
- Laravel Breeze (stack blade) — login, registro, logout, reset password
- Spatie Laravel Permission — roles: `Super-Admin`, `Veterinario`, `Recepcionista`
- Guard `api` con driver `session`
- `Gate::before` para Super-Admin
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
| | | _Por completar al implementar_ |

### Módulo: Users
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 3 — Catálogos base

### Módulo: Species
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Breeds
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Services
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Vaccine Types
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Medicines
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 4 — Dueños y mascotas

### Módulo: Owners
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Pacientes
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 5 — Horario y citas

### Módulo: Veterinarian Schedules
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Citas
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 6 — Módulo clínico

### Módulo: Vacunas
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Cirugías (Surgiere)
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Medical Records
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Prescriptions
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Vital Signs
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Medical Record Attachments
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 7 — Facturación y pagos

### Módulo: Invoices
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

### Módulo: Payments
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Fase 8 — Recordatorios

### Módulo: Reminders
| Elemento | Archivo | Estado |
|----------|---------|--------|
| | | _Por completar al implementar_ |

---

## Rutas registradas

### `routes/web.php` (Admin)
| Ruta | Controlador | Método | Nombre | Fase |
|------|-------------|--------|--------|------|
| _Se irán agregando fase por fase_ | | | | |

### `routes/api.php` (Api/Admin)
| Ruta | Controlador | Método | Nombre | Fase |
|------|-------------|--------|--------|------|
| _Se irán agregando fase por fase_ | | | | |

---

## Actions registradas
| Action | Módulo | Transacción | Fase |
|--------|--------|-------------|------|
| _Se irán agregando fase por fase_ | | | |

---

## Filters registrados (Pipeline)
| Filter | Módulo | Fase |
|--------|--------|------|
| _Se irán agregando fase por fase_ | | |

---

## Resources registrados
| Resource | Módulo | Fase |
|----------|--------|------|
| _Se irán agregando fase por fase_ | | |

---

## Vistas Blade
| Vista | Tipo | Módulo | Fase |
|-------|------|--------|------|
| _Se irán agregando fase por fase_ | | | |

---

## Decisiones técnicas

| Decisión | Contexto | Fase |
|----------|----------|------|
| _Se irán documentando_ | | |

---

## Fixes aplicados

| Fix | Archivo | Fecha | Referencia |
|-----|---------|-------|------------|
| _Se irán documentando_ | | | |
