# Dashboard con KPIs y gráficas (Chart.js) + Perfil reestilizado a Velzon

> Fecha: 2026-08-12 · Estado: aprobado por el usuario

## Contexto

El MVP del sistema veterinaria está completo (Fases 2–8 + Roles y Permisos). Quedan 2 pendientes para terminar el MVP:

1. **Perfil**: la página `profile/edit.blade.php` usa componentes y clases de Laravel Breeze (Tailwind: `py-12`, `max-w-7xl`, `bg-white shadow`, `x-*` components) que no coinciden con la plantilla Velzon (Bootstrap 5). Hay que reestilarla.
2. **Dashboard**: `resources/views/dashboard.blade.php` está vacío. Hay que llenarlo con KPIs profesionales y gráficas (Chart.js por CDN), con un selector de año que filtre toda la data vía AJAX.

## Decisiones acordadas

- **Filtro de año**: AJAX, sin recargar la página (patrón `window.ajax` del proyecto).
- **Alcance dashboard**: panel completo — 4 KPIs principales + 4 mini-KPIs + 4 gráficas.
- **Alcance perfil**: solo la página de perfil (info, contraseña, eliminar cuenta). Login/registro y componentes compartidos quedan intactos.
- **Chart.js**: por CDN, agregado en `resources/views/layouts/app.blade.php`.
- Textos visibles en español (convención del proyecto).

## Arquitectura

### Backend

- `app/Actions/Dashboard/ObtenerDashboardAction.php` — única dueña de las queries del dashboard; recibe `?year=` (null → año actual) y devuelve un array con `kpis` y `charts`.
- `Api/Admin/DashboardController@index` → devuelve `{success: true, data: {...}}` (mismo envelope agnóstico que los `index()`). Ruta `GET api/admin/dashboard` en `routes/api.php` (nombre `admin.api.dashboard`).
- `Admin/DashboardController@index` → reemplaza el closure de `/dashboard` en `routes/web.php`; pasa `$years` (años con data: invoices/citas/vacunas/cirugías) y `$selectedYear` (año actual o `?year=`).

### KPIs (del año seleccionado, salvo lo marcado)

| KPI | Query |
|-----|-------|
| Facturado S/ | `SUM(invoices.total)` con `issued_at` en el año |
| Cobrado S/ | `SUM(payments.amount)` con `paid_at` en el año |
| Por cobrar S/ | `SUM(invoices.remaining_balance)` de status `pendiente`+`parcial` (sin año) |
| Citas de hoy | citas con `appointment_date` = hoy y status ≠ cancelada |
| Pacientes totales | `Paciente::count()` (sin año) |
| Vacunas aplicadas | `Vacuna` con `vaccination_date` en el año |
| Cirugías | `Surgiere` con `surgery_date` en el año |
| Recordatorios pendientes | `Reminder` status `pendiente` (sin año) |

### Charts

1. **Ingresos mensuales** (bar, 12 meses): cobrado vs emitido por mes del año.
2. **Citas por estado** (doughnut): pendiente/confirmada/completada/cancelada del año.
3. **Pacientes por especie** (bar): count de pacientes agrupado por especie.
4. **Top veterinarios** (bar horizontal): citas completadas por veterinario del año (top 5).

### Frontend

- `resources/views/dashboard.blade.php`: `page-title-box` Velzon + selector `form-select` de años + fila de 4 KPI cards (icono Velzon `ri-*` + valor + etiqueta) + fila de 4 mini-KPI cards + grid de 4 contenedores de charts (`<canvas>`).
- `public/js/pages/dashboard.js`: `cargarDashboard(year)` vía `ajax.get("/admin/dashboard?year=" + year)` → actualiza KPIs y crea/actualiza los 4 charts de Chart.js (destruye instancias previas al cambiar año). Colores del tema Velzon (primary/success/warning/info).
- Script cargado solo en el dashboard vía `@push('scripts')`.

### Perfil (Velzon)

- `profile/edit.blade.php`: `page-title-box` + cards Bootstrap (estructura de `admin/*`).
- Partials: `form-label`, `form-control`, `btn btn-primary`, errores con `text-danger`, botón eliminar abre **modal Bootstrap** (reemplaza `x-modal` de Alpine). Mismo estado de sesión (`session('status')`) para mensajes "Guardado".
- Rutas/controladores sin cambios: `profile.update`, `password.update`, `profile.destroy`, `verification.send`.

### Layout

- Agregar Chart.js 4.x CDN (`https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js`) en `layouts/app.blade.php` antes de FullCalendar.

## Verificación

- `php -l` en Action/Controllers nuevos.
- `php artisan view:cache`.
- `node --check` en `public/js/pages/dashboard.js`.
- Tinker/HTTP: `GET /api/admin/dashboard?year=2026` responde `{success: true, data: {kpis, charts}}` coherente con los seeders.
- Render de `dashboard.blade.php` y `profile/edit.blade.php` sin errores.
