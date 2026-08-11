# Fix #8 — UI del módulo Vacunas replicando Citas (filtros + disponibilidad + pago)

**Tipo:** Mejora de módulo (backend + frontend) + bugfix preexistente en CRUD vaccine-types

**Fecha:** 2026-08-11

---

## Contexto

El usuario pidió rediseñar el módulo **Vacunas** para que replique el patrón de **Citas** (módulo ya consolidado):

1. **Index con filtros**: mascota, especie, veterinario, estado de pago y rango de fecha (desde–hasta).
2. **Formulario con disponibilidad de veterinarios**: seleccionar fecha → ver veterinarios con su horario → ver horas libres/ocupadas → elegir hora y veterinario (igual que Citas).
3. **Sección de Pago** en el formulario: método de pago, total (auto), adelanto.

## Decisión de diseño

Para poder replicar el selector de horas libres/ocupadas de Citas (que usa `appointment_time` TIME), se agregó a la tabla `vacunas` la columna **`vaccination_time`** (TIME, nullable) vía migración, y `base_price` a `vaccine_types` (decimal 10,2, default 0) porque el total de la vacuna sale del tipo de vacuna.

- Migración: `database/migrations/2026_08_11_000001_add_time_and_price_to_vacunas_and_vaccine_types.php`.
- Dump SQL (`db_sistema_veterinaria.sql`) actualizado con ambas columnas.
- Seeders actualizados: `VaccineTypeSeeder` (7 tipos con `base_price` 35–70) y `VacunaSeeder` (6 vacunas con `vaccination_time`).

## Cambios

### Backend — disponibilidad

- **`app/Actions/Vacunas/ValidarDisponibilidadVacuna.php`** (nuevo): valida que (a) el vet tenga horario activo ese día de la semana (`start <= time < end`), (b) no exista otra cita activa (pendiente/confirmada) del mismo vet en la misma fecha+hora y (c) no exista otra vacuna del mismo vet en la misma fecha+hora, ignorando `$ignorarVacunaId` (update). Lanza `ValidationException` con mensaje específico ("El veterinario ya tiene una cita/vacuna a esa hora.").
- **`app/Actions/Citas/ObtenerDisponibilidadAction.php`**: ahora la agenda de horas ocupadas **une citas + vacunas** (con `vaccination_time`), así el selector de horas muestra ocupado tanto por citas como por vacunas.
- `StoreVacunaRequest` / `UpdateVacunaRequest`: reglas `vaccination_time` (H:i), `payment_method` y `advance_amount` obligatorias + `after()` que llama a `ValidarDisponibilidadVacuna`.

### Backend — transacción y pago

- **`CreateVacunaAction`**: TX que crea vacuna + `medical_record` (event_type `vacuna`) + `reminder` (si next_due_date) + **invoice** (`invoiceable_type` = `vacuna`, total = `vaccine_types.base_price`) + **payment** (estado según adelanto: `pagado`/`parcial`/`pendiente`; `advance_amount`, `payment_method`, `paid_at`).
- **`UpdateVacunaAction`**: TX que actualiza la vacuna y hace **upsert** de invoice/pago (recalcula según nuevo adelanto).

### Backend — filtros y listado

- **`app/Filters/Vacunas/`** (nuevos): `FiltrarPorBusquedaVacunas`, `FiltrarPorEspecieVacuna`, `FiltrarPorVeterinarioVacuna`, `FiltrarPorEstadoPagoVacuna`, `FiltrarPorFechaVacuna`.
- **`ListVacunasAction`**: pipeline completo + eager load `paciente, user, vaccine_type, invoice, invoice.payments`.
- **`VacunaResource`**: añade `species`, `vaccination_time`, `vaccine_price`, `payment_status`, `payment_total`, `payment_paid`.

### API + rutas

- **`Api/Admin/VacunaController`**: método `disponibilidad` (usa `ObtenerDisponibilidadAction`) y `show` añadidos.
- **`routes/api.php`**: `GET api/admin/vacunas/disponibilidad` (name `admin.api.vacunas.disponibilidad`), registrada **antes** del `apiResource` para no colisionar con `{vacuna}`.

### Frontend

- **`Admin/VacunaController`**: `index` con `veterinarians`, `species`, `paymentStatuses`; `create`/`edit` con `pacientesData` (preview de mascota), `vaccineTypes` (Eloquent con `base_price`) y la invoice (para edición).
- **Vistas** `admin/vacunas/{index,create,edit}.blade.php`:
  - `index`: filtros (mascota/especie/veterinario/estado de pago/fecha desde–hasta) + columna Pago con badges de estado.
  - `create`/`edit`: réplicas de Citas — bloque de disponibilidad (fecha → vet-card → horas), preview de mascota, sección Pago (total readonly, método, adelanto).
- **`public/js/pages/vacunas.js`** reescrito: DataTable serverSide + filtros (debounce en búsqueda) + `initVacunaForm` (carga disponibilidad, `aplicarVet`, preselección en edición, validación adelanto ≤ total, submit con ajax).

### CRUD vaccine-types con precio

- `base_price` en `Store/UpdateVaccineTypeRequest`, `Create/UpdateVaccineTypeAction`, `VaccineTypeResource`, vistas `create/edit/index` (columna Precio) y `vaccine-types.js`.

## Bug preexistente corregido de paso

**`app/Http/Requests/VaccineTypes/UpdateVaccineTypeRequest.php`** (línea 21): la regla unique usaba

```php
"unique:vaccine_types,name,{$this->vaccineType->id}"
```

pero el parámetro de ruta es `{vaccine_type}` (snake_case, generado por `apiResource('vaccine-types')`), por lo que `$this->vaccineType` era `null` → `ErrorException: Attempt to read property "id" on null` al intentar actualizar por HTTP. Corregido a:

```php
"unique:vaccine_types,name,{$this->route('vaccine_type')?->id}"
```

(El binding implícito del modelo se resuelve por el nombre del parámetro de ruta, no por la propiedad `vaccineType`.)

## Bug del update de Vacunas corregido (causa raíz: fechas pasadas bloqueadas)

El usuario reportó que la vista de edición de Vacunas estaba rota / el update no funcionaba. **Causa raíz:** `UpdateVacunaRequest` agregó la regla `after_or_equal:today` en `vaccination_date`, pero las 6 vacunas del seed tienen fechas pasadas (marzo–julio 2026; hoy agosto 2026) → **ninguna vacuna existente se podía editar** (422 "The vaccination date field must be a date after or equal to today."). La vista edit renderizaba bien, pero el guardado siempre fallaba.

**Fix:**

1. **`UpdateVacunaRequest`**: la regla de `vaccination_date` ahora es una Closure que rechaza fechas pasadas **excepto** si es la fecha original de la vacuna (`$this->route("vacuna")?->vaccination_date`). Así se puede editar una vacuna manteniendo su fecha pasada, pero no reprogramarla a otra fecha pasada. (En create se conserva `after_or_equal:today`.)
2. **`resources/views/admin/vacunas/edit.blade.php`**: el input `vaccination_date` tenía `min="{{ now()->toDateString() }}"`, que bloqueaba visualmente conservar una fecha pasada → ahora `min` es la fecha original si esta es pasada.
3. **`database/seeders/VacunaSeeder.php`**: la vacuna de Kiara estaba en `2026-06-28` (domingo, `dow=0`), día sin horario de veterinario → al editar manteniendo su fecha, `ValidarDisponibilidadVacuna` la rechazaba. Movida a `2026-06-26` (viernes) y corregida también en la BD.
4. **`resources/views/admin/vacunas/edit.blade.php`**: reordenados los scripts en `@push('scripts')` — el bloque que define `window.vacunasPacientesData` / `window.vacunaEditarInit` ahora va **antes** de cargar `vacunas.js` (el IIFE corría antes de que existieran los datos, por lo que el preview de la mascota y la preselección de disponibilidad no se aplicaban al cargar la página).

**Verificado:**
- HTTP real: PUT manteniendo la fecha original pasada → 200 OK (probadas las 6 vacunas del seed); PUT a otra fecha pasada → 422 correcto; PUT a fecha futura → 200.
- La vista edit renderiza 200 sin errores; el orden de scripts quedó: datos → `vacunas.js`.
- BD restaurada al estado limpio tras las pruebas (6 vacunas con su fecha/hora original, 6 invoices de vacuna `pendiente`, sin payments de prueba; 9 citas y sus payments intactos).

## Nuevo tipo de vacuna inline (replica el patrón `new_service` de Citas)

El usuario pidió que Vacunas permita registrar un **nuevo tipo de vacuna** desde el propio formulario (como Citas permite crear un servicio nuevo), en vez de tener que crearlo antes en el CRUD de vaccine-types.

**Cambios:**

1. **Requests** (`StoreVacunaRequest`, `UpdateVacunaRequest`): `vaccine_type_id` pasa a `required_without:new_vaccine_type` + nullable; se agrega `new_vaccine_type` como array con `name` (required), `base_price` (required, min 0) y `species_id` (nullable, exists:species).
2. **Actions** (`CreateVacunaAction`, `UpdateVacunaAction`): si no viene `vaccine_type_id` pero sí `new_vaccine_type`, crea el `VaccineType` dentro de la transacción y usa su id en la vacuna (el invoice se calcula con el `base_price` del nuevo tipo).
3. **Controller Admin** (`VacunaController`): `create`/`edit` ahora pasan `species` para el select de especie del nuevo tipo.
4. **Vistas** (`create`, `edit`): opción `＋ Crear nuevo tipo de vacuna` (valor `__nuevo__`) al final del select de tipo + bloque `bloqueNuevoTipoVacuna` con campos Nombre, Precio y Especie.
5. **`public/js/pages/vacunas.js`**: `selectTipo` muestra/oculta el bloque nuevo; `actualizarTotal()` usa el precio del nuevo tipo cuando está seleccionado (`__nuevo__`); `recolectarDatos()` envía `new_vaccine_type[name/base_price/species_id]` en lugar de `vaccine_type_id` cuando aplica.

**Verificado:**
- POST con `new_vaccine_type` → 201, vacuna creada con el tipo nuevo (invoice total = base_price del tipo nuevo).
- PUT con `new_vaccine_type` → 200, tipo nuevo creado y asignado (invoice `parcial`).
- 422 si no se envía ni `vaccine_type_id` ni `new_vaccine_type`.
- 422 si `new_vaccine_type.name` falta.
- Vistas create/edit renderizan 200 con el bloque nuevo.
- BD restaurada limpia tras las pruebas (7 tipos, 6 vacunas, 12 invoices, 6 payments, 9 citas).

## Calendario unificado (Citas + Vacunas + Cirugías) + correcciones

**Mejora:** el usuario pidió que el calendario mostrara las 3 cosas que tienen fecha (citas, vacunas, cirugías), cada una con su color, sin tocar los CRUD.

- Nuevo ítem de menú **"Calendario"** (sidebar, sección Clínica) → `GET /admin/calendario` (`admin.calendario.index`).
- `app/Actions/Calendario/CalendarioAction.php` (nuevo): devuelve citas, vacunas (con `invoice`) y cirugías (`Surgiere`) con todas sus relaciones, sin paginar.
- `app/Http/Resources/CalendarioEventResource.php` (nuevo): evento FullCalendar por modelo + `$tipo` (`id` prefijado `cita-`/`vacuna-`/`cirugia-`, `title`, `start`/`end`, `allDay` para vacunas sin hora, `color` y `extendedProps` con tipo/color/mascota/vet/detalle/status/notas/`edit_url`).
- Controllers nuevos: `Admin/CalendarioController@index` (vista) y `Api/Admin/CalendarioController@index` (une los 3 tipos con `collect()->merge()`).
- Rutas: `GET admin/calendario` (web) y `GET api/admin/calendario` (`admin.api.calendario`).
- Vista `admin.calendario.index` + `public/js/pages/calendario.js` (nuevos).
- **Color por tipo** con `className` (`ev-cita`/`ev-vacuna`/`ev-cirugia`) + CSS con `!important` en la vista (Citas `#405189`, Vacunas `#10b981`, Cirugías `#f59e0b`) — el tema Velzon fuerza `.fc-event-title` azul con `!important` y FullCalendar usa su azul por defecto si el inline no gana; la clase CSS lo garantiza.

**Corrección posterior (404 `api/api/admin/calendario`):** en `calendario.js` se llamaba `ajax.get("/api/admin/calendario")` pero el helper `ajax` (`public/js/config/ajax.js`) ya antepone `baseURL="/api"` → salía `/api/api/admin/calendario`. Fix: llamada pasó a `ajax.get("/admin/calendario")` (mismo patrón que `citas-calendar.js`).

**Corrección posterior (drawer sin guardar/cancelar + estado):** el usuario reportó que el drawer había perdido la opción de actualizar estado (quedó solo "Editar") y que todo el calendario salía del mismo color azul. Fix:
- Drawer: se quita el botón "Editar" y se agrega **select de estado** (según tipo: cita pendiente/confirmada/completada/cancelada; vacuna pendiente/parcial/pagado/anulado; cirugía pendiente/en_proceso/completada/cancelada) + botones **Guardar** y **Cancelar**.
- `calendario.js`: `poblarSelectEstado(tipo, actual)` + `RUTAS_ESTADO` → `PATCH /admin/citas/{id}/estado`, `PATCH /admin/cirugias/{id}/estado` (existían) y **nuevo** `PATCH /admin/vacunas/{id}/estado-pago`.
- `app/Actions/Vacunas/CambiarEstadoPagoVacunaAction.php` (nuevo): reusa `RegistrarPagoAction` (si `pagado` paga el saldo restante) y `AnularPagoAction` (si `pendiente` anula los pagos activos) dentro de una TX; soporta `parcial`/`anulado` directos.
- `Api/Admin/VacunaController@cambiarEstadoPago` + ruta `PATCH api/admin/vacunas/{vacuna}/estado-pago` (`admin.api.vacunas.estado-pago`) antes del apiResource.
- `CalendarioAction` eager-loada `invoice` en vacunas; `eventoVacuna` ahora expone `status`/`status_label` (estado de pago).

**Verificado por HTTP** (`php artisan serve --port=8123`, login dr.torres): vista `/admin/calendario` 200; `GET /api/admin/calendario` → 19 eventos (9 citas `#405189`, 7 vacunas `#10b981`, 3 cirugías `#f59e0b`) con `status` correcto y `className`; `PATCH /admin/vacunas/1/estado-pago` pendiente→pagado→pendiente OK (invoice vuelve a pendiente con saldo 35 y pago anulado); `PATCH /admin/citas/2/estado` y `/admin/cirugias/3/estado` OK; todo restaurado a su estado original. `php -l`, `node --check`, `view:cache` OK.

## Fix #9 — Módulo Cirugías replicando Vacunas/Citas (filtros del index + pago/factura en el create)

**Tipo:** Mejora de módulo (backend + frontend)

**Fecha:** 2026-08-11

**Contexto:** El usuario pidió adaptar el módulo de **Cirugías** (`admin/cirugias/`) para que sea similar a Vacunas y Citas:
1. **Index con filtros**: nombre de mascota (búsqueda), especie, veterinario, estado de pago y rango de fecha (desde–hasta).
2. **Create** con selección de mascota (con preview), form de cirugía y bloque **Pago/Factura obligatorio** (método + adelanto), con el **total ingresado manualmente** porque la cirugía no tiene catálogo/precio base.

### Decisión de diseño

A diferencia de Vacunas (el total sale de `vaccine_types.base_price`), la cirugía **no tiene precio base**: el usuario ingresa el **total manualmente** en el form. Se replica el patrón de facturación polimórfica del proyecto (`invoiceable_type = 'surgiere'`, ya usado en 13 invoices previas).

### Backend

- **Filters nuevos** en `app/Filters/Cirugias/`: `FiltrarPorBusquedaCirugia` (mascota, veterinario, `surgery_type`), `FiltrarPorEspecieCirugia` (por `paciente.species_id`), `FiltrarPorVeterinarioCirugia`, `FiltrarPorEstadoPagoCirugia` (por invoice `surgiere`), `FiltrarPorFechaCirugia` (`surgery_date_from/to`).
- **`ListCirugiasAction`**: pipeline de los 5 filters + `OrdenarPor` + eager load `paciente, user, invoice, invoice.payments`.
- **Modelo `Surgiere`**: relación `invoice()` (`hasOne` con `invoiceable_type='surgiere'`).
- **`CirugiaResource`**: expone `species_id`, `species`, `surgery_time`, `payment_status`, `payment_total`, `payment_paid`.
- **`Store/UpdateCirugiaRequest`**: `total` (required, min 0), `payment_method` (required, enum), `advance_amount` (required, min 0.01).
- **`Create/UpdateCirugiaAction`**: TX que crea/actualiza cirugía + `medical_record` (event_type `cirugia`; create) + `registrarPago()` privado que hace upsert de Invoice (`surgiere`, status `pagado`/`parcial`/`pendiente` según adelanto) + Payment.
- **`CambiarEstadoPagoCirugiaAction`** (nuevo) + endpoint `PATCH api/admin/cirugias/{cirugia}/estado-pago`: replica vacunas — `pagado` usa `RegistrarPagoAction` (paga saldo restante), `pendiente` usa `AnularPagoAction`, `anulado` actualiza invoice.

### Web / Frontend

- **`Admin/CirugiaController`**: `index` pasa `veterinarians`, `species`, `paymentStatuses`; `create`/`edit` pasan `pacientesData` (preview) e `invoice` con `payments` (edición).
- **Vistas** `admin/cirugias/{index,create,edit}.blade.php`: index con form de filtros + columna **Pago**; create/edit con preview de mascota + bloque de pago (total manual, método, adelanto).
- **`public/js/pages/cirugias.js`**: `recolectarFiltros()`/`getFilters()` server-side (debounce 350 ms, Filtrar/Limpiar), `initPreviewMascota()`, envío de `total`/`payment_method`/`advance_amount`, columna Pago con badge + dropdown de cambio de estado de pago.

### Verificación (HTTP real, `php artisan serve` 127.0.0.1:8000)

- `php -l` OK (17 archivos), `view:cache` + `view:clear` OK, `node --check` cirugias.js OK. Sin SEMAPHORE/commits.
- `GET /api/admin/cirugias?per_page=5` → contrato `{success, data, pagination}` con `payment_status` (parcial etc.).
- Filtros: `payment_status=parcial`→2, `species_id=1`→2 (Perro), `search=Rocky`→1. Vista index/create/edit → 200 (form de filtros y bloque de pago presentes).
- `PATCH estado-pago` cirugía 2 parcial→**pagado** (payment_total 150, payment_paid 150) y revertida manualmente a **parcial** (saldo 75, 1 pago tarjeta).
- `POST` create con total 200/adelanto 80 → 201 con invoice `parcial` (total 200, paid 80); `PUT` update → `pagado` 250/250. `DELETE` → 422 (regla de historial médico, correcta).
- Limpieza tras pruebas: datos de prueba eliminados manualmente (invoice + payment + medical_record + cirugía 4). **BD restaurada: 13 invoices, 7 payments, 3 cirugías.**

## Fix #10 — Correcciones post-revisión: DataTable de cirugías ("unknown parameter estado") + tabs del show de paciente ("undefined relationship veterinarian")

**Tipo:** Bugfix (frontend + API) · **Fecha:** 2026-08-11

### 1. DataTable de cirugías — "Requested unknown parameter 'estado' for row 0, column 6"

**Síntoma:** warning de DataTables en `admin/cirugias`: la columna 6 (Estado) no encontraba el parámetro `estado`.

**Causa raíz:** en `public/js/pages/cirugias.js` la columna del index se declaró como `{ data: "estado", orderable: false }`, pero `datosCargados()` retornaba la clave `status` (no `estado`) y el estado no tenía badge de color.

**Fix:** en `datosCargados()`, en `public/js/pages/cirugias.js`, se añadió la clave `estado: renderEstado(c.status)` (genera el badge con color y etiqueta traducida), igual que la columna Pago.

### 2. Tabs del show de paciente — "Call to undefined relationship [veterinarian] on model [App\Models\MedicalRecord/Vacuna]"

**Síntoma:** en `admin/pacientes/12` los 3 tabs (Historial médico, Vacunas, Cirugías) mostraban "Ocurrió un error: Call to undefined relationship [veterinarian]".

**Causa raíz:** en `app/Http/Controllers/Api/Admin/PacienteController.php`, `records()`, `vacunas()` y `cirugias()` usaban `with("veterinarian")` + `$modelo->veterinarian?->username`, pero `MedicalRecord`, `Vacuna` y `Surgiere` definen la relación como `user()` (no `veterinarian()`), lanzando `RelationNotFoundException`.

**Fix:** en `PacienteController.php` los tres métodos pasaron a `with("user")` y `$modelo->user?->username`. La API **mantiene la clave `veterinarian`** en la respuesta (no se toca `paciente-ficha.js`); solo cambia la relación interna del modelo.

### Verificación (HTTP real, `php artisan serve` 127.0.0.1:8000, sesión carlos.torres)

- `node --check public/js/pages/cirugias.js` OK; `php -l PacienteController.php` OK.
- `GET /api/admin/pacientes/12/records` → 200 con `veterinarian` (dr.paredes); `/vacunas` → 200 con `vaccine_type`; `/cirugias` → 200.
- DataTable de cirugías sin el warning de `estado` (recarga de `admin/cirugias`).

**Nota OPcache:** el server con `revalidate_freq=180` puede conservar la versión anterior del controlador por ~3 min tras editar; si el error persiste en el navegador, recargar pasado ese rato o reiniciar el servidor.

## Fix #11 — Bloque de disponibilidad en el create/edit de Cirugías (select de fecha → veterinarios con horario → horas libres)

**Tipo:** Mejora de UI/UX · **Fecha:** 2026-08-11

**Contexto:** El usuario reportó que el create de Cirugías no se parecía al de Citas/Vacunas: faltaba el selector de disponibilidad del médico. "Corrige eso". Se replicó el patrón de Vacunas completo.

**Backend:**
- **`app/Actions/Cirugias/ObtenerDisponibilidadCirugiaAction.php`** (nuevo): igual que `ObtenerDisponibilidadAction` pero la agenda ocupada une **citas + vacunas + cirugías activas**.
- **`Api/Admin/CirugiaController@disponibilidad`** + ruta `GET api/admin/cirugias/disponibilidad` antes del apiResource.
- **`app/Actions/Cirugias/ValidarDisponibilidadCirugia.php`** (nuevo): horario activo + no cita/vacuna/cirugía a esa hora (ignora la cirugía actual en update). En edición, si no cambió fecha/hora/vet, permite guardar (para fechas pasadas del seed).
- **Requests**: `surgery_time` requerido (`H:i`) y `after()` valida disponibilidad. Update lee el id de ruta robustamente (`is_object`).
- **Actions Create/Update**: `armarFechaHora()` combina `surgery_date` (Y-m-d) + `surgery_time` (H:i) → datetime; `issued_at` de la invoice con esa fecha.

**Frontend:**
- **Vistas create/edit**: fecha (disparador) → tarjetas `vet-card` (horario + badge Disponible/Ocupado/Sin horario + botón Aplicar) → horas libres → hidden `veterinarian_id` + `surgery_time`; preview de mascota; sección Pago; "Guardar y cobrar". Edit pasa `window.cirugiaEditarInit = {veterinario, hora}`.
- **`public/js/pages/cirugias.js`**: `initCirugiaForm()` replicando `initVacunaForm` (cargarDisponibilidad, renderVetCard, bindVetCards, aplicarVet, renderHoras, preseleccionarEnEdicion, adelanto ≤ total, submit con fecha+hora+vet).

**Verificado por HTTP** (`php artisan serve` 127.0.0.1:8000, carlos.torres):
- `GET /api/admin/cirugias/disponibilidad?fecha=2026-08-18` → 3 vets con horario+estado+slots.
- POST válido → 201 `2026-08-18 10:00` pago parcial; hora fuera de horario → 422; hora ocupada por cirugía → 422.
- PUT manteniendo fecha/hora → 200 (self-check); cirugía seed del sábado sin horario → 200; hora conflictiva → 422.
- Vistas create/edit → 200 con bloque disponible. BD restaurada (13 invoices, 7 payments, 3 cirugías; cirugía 3 `2026-08-01 11:00`). `php -l`, `node --check`, `view:cache` OK.

## Verificación

- `php -l` OK en todos los archivos PHP tocados; `node --check` OK en `vacunas.js` y `vaccine-types.js`; `view:cache` OK; `route:list` confirma las rutas nuevas.
- HTTP real con `php artisan serve --port=8123`:
  - `GET /api/admin/vacunas/disponibilidad?fecha=2026-05-15` → la hora `09:30` de dr.torres aparece **ocupada** (vacuna de Rocky).
  - `POST /api/admin/vacunas` con pago → 201, invoice creada con estado `pagado`.
  - `POST` con hora ocupada por otra vacuna → 422 "El veterinario ya tiene una vacuna a esa hora.".
  - `POST` con hora ocupada por una cita → 422 "El veterinario ya tiene una cita a esa hora.".
  - `PUT` cambia pago a parcial → 200 con invoice `parcial`.
  - Filtros search/especie/veterinario/estado de pago/fecha → OK.
  - CRUD vaccine-types: create con precio OK; **update con precio OK tras corregir el bug**; delete OK.
- Limpieza: vacuna de prueba (id 7) y tipos de prueba (ids 9, 10) eliminados. BD final: 7 vaccine_types, 6 vacunas, 12 invoices, 9 citas (intactas).
- Servidor `php artisan serve` detenido al cerrar.

## Estado

Completo y verificado por HTTP real. Pendiente de revisión visual manual del usuario en navegador.