# Fix #3 — Error en Medical Records index + rutas Invoices/Pagos inconsistentes

**Tipo:** Fix de bug + fix de UI/routing, sin nueva funcionalidad

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/02_fix.md`

---

## Problema 1 — Error en `resources/views/admin/medical-records/index.blade.php:5`

### Qué se encontró

La vista lanzaba `ErrorException` en la línea 5 (`<h4 class="mb-sm-0">{{ $title }}</h4>`).

**Causa raíz:** el controller `app/Http/Controllers/Admin/MedicalRecordController` no definía la variable `$title` en **ninguno** de sus tres métodos (`index`, `create`, `show`). Las tres vistas del módulo (`admin/medical-records/{index,create,show}.blade.php`) renderizan `{{ $title }}` en su `page-title-box` (línea 5), pero el controller solo pasaba `records`/`pacientes` (index), `pacientes`/`veterinarios`/`medicines`/`citas` (create) y `record` (show).

Esto rompía el patrón del resto del módulo admin: los demás `Admin/*Controller` pasan `"title" => ...` (verificado en InvoiceController, MedicineController, VaccineTypeController, CirugiaController, ServiceController, VeterinarianScheduleController, BreedController, PacienteController, VacunaController, SpeciesController, OwnerController, UserController, BranchController y CitaController). Era una variable no pasada desde el controller; no había problema de relación no cargada ni de colección `null`.

### Qué se corrigió exactamente

En `app/Http/Controllers/Admin/MedicalRecordController.php` se pasó explícitamente `title` en los tres métodos:

- `index` → `"title" => "Historial Médico"`
- `create` → `"title" => "Nueva Entrada de Historial"`
- `show` → `"title" => "Detalle del Historial"`

Se reemplazaron los `compact(...)` por arrays explícitos con `title` + datos.

### Empty state

Verificado: el `index.blade.php` de medical-records no itera `$records` directamente en el render — la tabla `#table-medical-records` se puebla vía AJAX desde `js/pages/medical-records.js` con el endpoint `admin.api.medical-records.index`, y el empty state lo maneja DataTables (mensaje "No hay datos" del locale es-ES) cuando la respuesta trae arreglo vacío. La variable `$pacientes` (filtro) sí se usa en la vista y ya se pasaba. Se confirmó el render real sin errores (`view('admin.medical-records.index')` compila OK).

---

## Problema 2 — `/admin/invoices` vs `/admin/invoices#pagos`

### Qué se encontró

El sidebar (`resources/views/layouts/app.blade.php`) tenía **dos** entradas en el bloque Facturación:

- "Facturas" → `admin.invoices.index`
- "Pagos" → `admin.invoices.index#pagos`

`#pagos` es un fragmento de URL (ancla), **no genera ruta ni controller distinto**: ambos enlaces cargaban exactamente el mismo `admin.invoices.index` completo, sin diferenciar nada. A nivel de usuario parecía un link roto o un segundo menú que no lleva a ningún lado real.

### Qué opción se eligió y por qué

Se eligió la **Opción recomendada: una sola vista `admin/invoices/index.blade.php` con tabs Bootstrap**, consistente con el patrón de vista única con tabs vía AJAX que ya se usa en `admin/pacientes/show.blade.php`.

Motivo: era la opción que menos código duplicado generaba dado lo existente de Fase 7. La alternativa (página real `/admin/invoices/payments` con controller/vista propia) implicaba crear una ruta web, un método, una vista y un JS adicionales — más superficie que mantener para un listado que comparte el mismo dominio. Con tabs solo se añadió un `index` al `Api/Admin/PaymentController` (reutilizando `PaymentResource`) y un tab en la vista existente.

### Qué se corrigió exactamente

1. **`routes/api.php`** — nueva ruta `GET admin-api/payments` → `admin.api.payments.index` (listado de pagos, cargando `invoice.owner`, con filtro opcional por `status`).

2. **`app/Http/Controllers/Api/Admin/PaymentController.php`** — nuevo método `index(Request $request)`: consulta `Payment::with(["invoice.owner"])`, filtra por `status` si viene, ordena por `paid_at` desc, responde con `PaymentResource::collection`.

3. **`app/Http/Resources/PaymentResource.php`** — se enriqueció el recurso con datos de la factura para el tab de Pagos: `invoice_label` ("Factura #ID"), `owner_name` (del dueño de la factura, `whenLoaded("invoice")`) e `invoice_url` (ruta `admin.invoices.show`). Se evitó N+1: no se hace lookup por `invoiceable` por fila.

4. **`resources/views/admin/facturacion/invoices/index.blade.php`** — el contenido se reorganizó dentro de tabs Velzon (`nav-tabs nav-tabs-custom`):
   - Tab **Facturas** (`#tab-invoices`, activo por defecto): formulario de filtros + `#table-invoices` (como estaba).
   - Tab **Pagos** (`#tab-payments`): formulario de filtros por estado (Pagado/Anulado) + `#table-payments` con columnas ID, Factura, Dueño, Monto, Método, Estado, Fecha, Acciones.
   - El botón "Nueva factura" quedó dentro del tab Facturas.

5. **`public/js/pages/invoices.js`** — se añadió:
   - `cargarPagos()`: AJAX a `/admin-api/payments` + relleno de `#table-payments` (mismo patrón que `cargarDatos()` de facturas).
   - `construirDataTable()` ahora también inicializa `#table-payments`.
   - `#formFiltrosPagos` con submit → `cargarPagos()`.
   - `activarTabDesdeHash()`: si `location.hash === "#pagos"`, activa el tab de Pagos con `$('[data-tab="payments"]').tab("show")`.
   - Se llama `cargarPagos()` al init.

6. **`resources/views/layouts/app.blade.php`** — se eliminó el segundo entry del sidebar ("Pagos" apuntando a `admin.invoices.index#pagos`). Queda **una sola entrada** "Facturas" → `admin.invoices.index`. El tab de Pagos se alcanza desde la propia vista (tabs) o con `#pagos` en la URL (manejado por JS, no por un segundo `<a href>`).

---

## Motores de cambio
- `app/Http/Controllers/Admin/MedicalRecordController.php` (title en index/create/show).
- `app/Http/Controllers/Api/Admin/PaymentController.php` (método `index`).
- `app/Http/Resources/PaymentResource.php` (datos de factura).
- `routes/api.php` (ruta `admin.api.payments.index`).
- `resources/views/admin/facturacion/invoices/index.blade.php` (tabs Facturas/Pagos).
- `public/js/pages/invoices.js` (carga de pagos + hash).
- `resources/views/layouts/app.blade.php` (single entry Facturas).

## Estado de la carga de vistas
- `php -l` OK en los 3 archivos PHP tocados.
- `php artisan route:list --path=payments` OK (`admin.api.payments.index` presente).
- `php artisan view:cache` pasado con éxito (Blade OK).
- Render verificado vía tinker: `admin.medical-records.index` y `admin.facturacion.invoices.index` compilan sin errores (tabs y `table-payments` presentes).
- `node --check public/js/pages/invoices.js` OK.
- Endpoint de pagos probado vía tinker: devuelve `invoice_label`, `owner_name` e `invoice_url` correctos.
