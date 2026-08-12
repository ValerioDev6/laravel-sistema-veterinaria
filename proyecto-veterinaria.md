# Proyecto: Sistema Veterinaria

## ⚠️ Estado actual del proyecto (leer antes de planificar)

- **Autenticación (Laravel Breeze) y RBAC (Spatie Laravel Permission) YA ESTÁN IMPLEMENTADOS Y FUNCIONANDO.** Login, registro, logout, reset password, roles (`Super-Admin`, `Veterinario`, `Recepcionista`), permisos, middleware de protección en `/admin` y `Gate::before` — todo eso ya existe en el proyecto.
- **No se debe tocar, regenerar, ni modificar nada relacionado a auth o permisos.** No incluir esas tareas en el plan de implementación. No crear vistas de login/registro, no tocar `AuthServiceProvider`, no crear "vista de permisos por rol" ni nada similar.
- Los **modelos Eloquent ya están creados** (según la base de datos exportada, ver abajo). No los regeneres ni los reescribas desde cero — solo revísalos y, si falta algo puntual (relaciones, casts, fillable), complétalo sin romper lo existente.
- El **sidebar, topbar y layout base (Velzon)** ya están montados. No se rediseña ni se recrea maquetado — solo se cablea con datos reales.
- **`resources/views/admin/dashboard.blade.php` se queda como placeholder vacío, tal como está.** No se implementa contenido de dashboard en ninguna fase de este plan. Esto lo hace el usuario aparte.
- El alcance de este plan es **exclusivamente los módulos de negocio de la veterinaria** (las 20 tablas del modelo de datos, sin contar las tablas propias de auth/Spatie/Laravel). Todo lo que sí corresponde construir: CRUDs, listados, filtros, transacciones, historial clínico, agenda, facturación, recordatorios.

---

## Rol del agente

Eres un desarrollador fullstack senior con dominio experto en Laravel MVC tradicional, diseño de bases de datos relacionales, y construcción de paneles administrativos con Bootstrap + DataTables. Trabajas con criterio propio: si ves algo que se puede hacer mejor, lo dices antes de implementar. Tu código es limpio, mantenible y profesional. No necesitas que te expliquen conceptos básicos.

---

## OBJETIVO GENERAL

Sistema de gestión para una clínica veterinaria, administrado desde un panel privado por el personal (veterinarios, recepción, administración). No es un sistema público tipo ecommerce — es un panel interno de gestión clínica y operativa:

- Gestión de mascotas y sus dueños
- Historial clínico completo por mascota (consultas, vacunas, cirugías, signos vitales)
- Agenda de citas y horario semanal por veterinario
- Vacunación con recordatorios de próxima dosis
- Cirugías con seguimiento de estado
- Facturación y pagos (con soporte para pagos parciales)
- Inventario básico de medicamentos y recetas
- Multi-sede (branches), si la clínica crece a más de un local

> La gestión de usuarios internos (crear/editar personal, asignar rol ya existente desde un select) sí entra como CRUD simple de la tabla `users`, pero **no incluye construir ni tocar el sistema de permisos/roles en sí** — eso ya está resuelto por Spatie.

El objetivo final es que el personal de la clínica gestione el día a día (citas, historiales, cobros) desde un panel simple, sin necesidad de conocimientos técnicos.

---

## CONSIDERACIONES GENERALES

Aplican SIEMPRE a todas las fases:

- Todas las rutas del panel empiezan en `/admin`
- Todas las acciones del panel requieren autenticación (guard `api`, ya configurado — no se toca)
- Mostrar mensajes de confirmación con **SweetAlert2** tras cada acción (crear, editar, eliminar)
- **Confirmación obligatoria con SweetAlert2 antes de cualquier acción destructiva** (eliminar paciente, cancelar cita, eliminar usuario, anular factura, etc.) — no negociable, no se elimina nada sin ese paso.
- **Crear y editar SIEMPRE van en formulario/vista separados, nunca el mismo modal o form reutilizado alternando entre modo "crear" y modo "editar".** Cada uno con su propio partial Blade y su propio archivo JS de página (o su propia función bien separada dentro del mismo archivo de módulo, pero nunca un único formulario que cambia de comportamiento según una bandera oculta).
- Si no existen datos en una sección, mostrar un "empty state" agradable (no solo una tabla vacía)
- No romper funcionalidades de fases anteriores
- Mantener consistencia visual en todo el panel (mismo layout Bootstrap/Velzon en todas las vistas)
- Manejo de errores con mensajes claros para el usuario (nunca mostrar stacktrace ni errores técnicos crudos — capturar y mostrar vía SweetAlert2)
- Zona horaria del sistema: `America/Lima` (Lima, Perú) — aplica a todas las fechas, horas, citas y reportes

---

## ARQUITECTURA GENERAL

**Monolito Laravel MVC** — no hay separación backend/frontend como proyectos independientes. Todo vive en un solo proyecto Laravel:

- Rutas en `routes/web.php` (navegación entre páginas, controladores devuelven vistas Blade)
- Rutas en `routes/api.php` para las acciones puntuales consumidas vía AJAX (crear/editar/eliminar sin recargar página), protegidas con guard `api` (ya configurado)
- Vistas Blade con Bootstrap 5 puro (sin frameworks JS adicionales, sin build de assets vía npm — todo por CDN, ver sección **CDN**)
- **AJAX** para todas las peticiones dentro de una página (formularios, tablas y filtros), implementado de forma profesional, sin recargar la página.
- Navegación entre secciones del panel SÍ recarga la página (comportamiento MVC normal) — solo las acciones puntuales (guardar, eliminar, cambiar estado) van por AJAX
- DataTables para todas las tablas/listados del panel (paginación, búsqueda y orden del lado del servidor cuando el volumen lo amerite). jQuery ya está cargado como dependencia de DataTables. Las peticiones AJAX se implementan con **`$.ajax`** (jQuery), envuelto en una capa JavaScript modular y reutilizable — nunca `$.ajax` suelto y repetido en cada página, ni `fetch` directo.
- SweetAlert2 para confirmaciones y mensajes de resultado

### Patrón de controladores: Web vs Api

Cada módulo tiene **dos controladores separados**, no uno solo que mezcle vistas y JSON:

```
app/Http/Controllers/
  Admin/
    PacienteController.php      # solo index()/show() que devuelven vistas Blade
    CitaController.php
    ...
  Api/
    Admin/
      PacienteController.php    # store/update/destroy/status → responden JSON, consumidos vía AJAX
      CitaController.php
      ...
```

- El controlador `Admin/*` es "tonto": arma la vista y le pasa datos mínimos (catálogos para selects, etc.). No contiene lógica de negocio.
- El controlador `Api/Admin/*` es el que recibe las peticiones AJAX (crear, editar, eliminar, cambiar estado). Tampoco contiene lógica de negocio — delega a una **Action**.
- `routes/web.php` apunta a `Admin/*`. `routes/api.php` apunta a `Api/Admin/*`.

### Actions (lógica de negocio fuera del controlador)

Controladores limpios: reciben el Form Request ya validado, llaman a una Action, devuelven la respuesta. Nada de queries, nada de `if` de negocio dentro del controlador.

```
app/Actions/
  Pacientes/
    CreatePacienteAction.php
    UpdatePacienteAction.php
    DeletePacienteAction.php
  Citas/
    CreateCitaAction.php
    CambiarEstadoCitaAction.php
  Facturacion/
    GenerarInvoiceAction.php
    RegistrarPagoAction.php
  ...
```

- Una Action = una responsabilidad, método `__invoke` o `execute`.
- Ejemplo de firma en el controlador:

```php
public function store(StorePacienteRequest $request, CreatePacienteAction $action)
{
    $paciente = $action->execute($request->validated());

    return response()->json([
        'status' => true,
        'message' => 'Paciente creado correctamente',
        'data' => new PacienteResource($paciente),
        'errors' => (object) [],
    ]);
}
```

### Transacciones de base de datos

**Toda Action que escriba en 2 o más tablas debe envolver su lógica en `DB::transaction()`.** Ejemplos que obligatoriamente llevan transacción:

- Crear cita + generar reminder asociado
- Registrar vacuna + insertar en `medical_record` + generar reminder de próxima dosis
- Registrar cirugía + insertar en `medical_record`
- Generar `invoice` + su primer `payment` (si aplica)
- Registrar `payment` + actualizar `remaining_balance`/`status` del `invoice`
- Crear `medical_record` + `prescriptions` + `vital_signs` en el mismo submit

Una Action que solo toca una tabla (CRUD simple de `species`, `owners`, etc.) no necesita transacción explícita.

### Filtros: patrón Pipeline (nada de scopes)

Para listados filtrables (citas por veterinario/fecha/estado, facturas por estado, etc.) se usa el **patrón Pipeline de Laravel**, no query scopes en el modelo.

```
app/Filters/
  Citas/
    FiltrarPorVeterinario.php
    FiltrarPorFecha.php
    FiltrarPorEstado.php
  Facturas/
    FiltrarPorEstado.php
    FiltrarPorRangoFecha.php
```

Cada filtro es un `invokable class` con firma `handle($query, Closure $next)`. En el controlador Api o en la Action de listado:

```php
$query = Pipeline::send(Cita::query())
    ->through([
        new FiltrarPorVeterinario($request->veterinario_id),
        new FiltrarPorFecha($request->fecha),
        new FiltrarPorEstado($request->status),
    ])
    ->thenReturn();
```

Los filtros solo se aplican si el parámetro viene presente en el request (cada filtro decide internamente si modifica el query o solo llama a `$next($query)`).

### API Resources

Toda respuesta JSON que devuelva un modelo o colección pasa por un **API Resource** (`app/Http/Resources/`). Nunca se devuelve un modelo Eloquent crudo en el `data` de la respuesta.

```
app/Http/Resources/
  PacienteResource.php
  CitaResource.php
  InvoiceResource.php
  ...
```

### Validaciones

Toda regla de negocio y validación (incluyendo unicidad, ej. `username` ya existe) vive en **Form Requests**, nunca inline en el controlador ni en la Action.

```
app/Http/Requests/
  Pacientes/
    StorePacienteRequest.php
    UpdatePacienteRequest.php
  ...
```

> Nota: existe un `Store*Request` y un `Update*Request` separados por módulo — esto es consistente con la regla de "crear y editar nunca comparten el mismo form/vista": tampoco comparten la misma clase de validación cuando las reglas difieren (por ejemplo, unicidad ignorando el propio registro en edición).

### Autenticación y permisos — YA IMPLEMENTADO, NO TOCAR

- Laravel Breeze (stack `blade`), guard `api` con driver `session`, Spatie Laravel Permission, roles base (`Super-Admin`, `Veterinario`, `Recepcionista`) y `Gate::before` ya existen y funcionan en el proyecto.
- Este plan **no incluye ninguna tarea sobre este bloque**. Si al construir un módulo se necesita saber si un usuario tiene cierto permiso, se usa lo ya existente (`@can`, middleware `can:`, etc.) sin modificarlo.

---

## MODELO DE DATOS (20 tablas, ya migradas y con modelos ya creados)

> Base de datos exportada de referencia: `db_sistema_veterinario`. No modificar el esquema salvo que se detecte un problema real — en ese caso, avisar antes de tocarlo.

| #   | Tabla                        | Propósito                                         |
| --- | ---------------------------- | ------------------------------------------------- |
| 1   | `branches`                   | Sedes/sucursales                                  |
| 2   | `users`                      | Personal (veterinarios, recepción, admin) — auth  |
| 3   | `species`                    | Catálogo de especies                              |
| 4   | `breeds`                     | Catálogo de razas, ligado a especie               |
| 5   | `owners`                     | Dueños de mascotas (no inician sesión)            |
| 6   | `pacientes`                  | Mascotas                                          |
| 7   | `veterinarian_schedules`     | Horario semanal por veterinario                   |
| 8   | `services`                   | Catálogo de servicios con precio base             |
| 9   | `vaccine_types`              | Catálogo de tipos de vacuna                       |
| 10  | `citas`                      | Citas agendadas                                   |
| 11  | `vacunas`                    | Aplicaciones de vacuna                            |
| 12  | `surgiere`                   | Procedimientos quirúrgicos                        |
| 13  | `medical_record`             | Historial médico consolidado                      |
| 14  | `medicines`                  | Catálogo de medicamentos                          |
| 15  | `prescriptions`              | Recetas ligadas al historial médico               |
| 16  | `vital_signs`                | Peso/temperatura/frecuencia por visita            |
| 17  | `medical_record_attachments` | Adjuntos del historial (fotos, PDFs)              |
| 18  | `invoices`                   | Facturas (polimórfica: cita/vacuna/cirugía)       |
| 19  | `payments`                   | Pagos aplicados a una factura (soporta parciales) |
| 20  | `reminders`                  | Recordatorios (próxima vacuna/cita)               |

Estas 20 tablas son el alcance completo del plan. Las tablas de Spatie (`permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`) y las propias de Laravel (`sessions`, `cache`, `jobs`, etc.) **no forman parte de ninguna tarea** — ya están resueltas.

---

## FUNCIONALIDADES DEL PANEL (`/admin`)

> Fase 1 (Auth y estructura base) queda fuera de este plan: ya está implementada. El plan arranca directamente en los módulos de negocio.

### Fase 2: Sedes y personal

- CRUD de `branches`
- CRUD de `users` (personal): crear, editar, activar/desactivar, asignar un rol ya existente desde un select simple (el rol se elige de la lista que ya está sembrada — no se crean roles ni permisos nuevos, no se construye pantalla de gestión de permisos)

### Fase 3: Catálogos base

- CRUD de `species` y `breeds` (ligadas a especie)
- CRUD de `services` (con precio base)
- CRUD de `vaccine_types`
- CRUD de `medicines` (con stock y costo)

### Fase 4: Dueños y mascotas

- CRUD de `owners`
- CRUD de `pacientes`, ligados a un `owner` + `species` + `breed`
- Vista de detalle de mascota: datos + historial médico + vacunas + cirugías (todo en una sola vista con tabs)

### Fase 5: Horario y citas

- CRUD de `veterinarian_schedules` (horario semanal por veterinario)
- Listado de `citas` con DataTable (filtros vía Pipeline: veterinario, fecha, estado)
- Vista de calendario de citas
- Crear/editar cita, cambiar estado (pendiente/confirmada/completada/cancelada) vía AJAX

### Fase 6: Módulo clínico

- Registrar vacuna (`vacunas`), asociada a `vaccine_types` → transacción (vacuna + medical_record + reminder + invoice/pago opcional) — la UI incluye disponibilidad de veterinarios (fecha → vets → horas libres/ocupadas, agenda unificada con citas) y sección de Pago (método, total = `base_price` del tipo, adelanto)
- Registrar cirugía (`surgiere`), con seguimiento de estado → transacción (cirugía + medical_record)
- Registrar entrada en `medical_record` (consulta, vacuna o cirugía)
- Agregar `prescriptions` (receta) y `vital_signs` (peso/temperatura) a un `medical_record`
- Subir adjuntos (`medical_record_attachments`)

### Fase 7: Facturación y pagos

- Generar `invoice` desde una cita/vacuna/cirugía completada
- Registrar `payments` contra una factura (soporta pagos parciales, método: efectivo/tarjeta/transferencia/otro) → transacción (payment + actualización de `remaining_balance`/`status` del invoice)
- Listado de facturas con estado (pendiente/pagado/parcial/anulado), filtros vía Pipeline

### Fase 8: Recordatorios — ✅ implementada 2026-08-11

- Listado de `reminders` pendientes (próximas vacunas/citas/cirugías) ✅
- Marcar como enviado/cancelado ✅

### Fase 9: Reportes básicos — **pendiente, no implementar aún**

- Reporte de citas por período/veterinario
- Reporte de facturación por período
- Stock bajo de medicinas
- Se definirá después si es exportación Excel/PDF o solo DataTable en pantalla. Por ahora **no generar código de esta fase**.

---

## STACK DE TECNOLOGÍA

- Backend: Laravel 12 (MVC tradicional, no API-first desacoplado)
- Frontend: Blade + Bootstrap 5 (sin build de assets — todo vía CDN)
- Interactividad: AJAX nativo modular (`XMLHttpRequest`), con una capa reutilizable para todas las peticiones. jQuery se utiliza únicamente como dependencia de DataTables.
- Confirmaciones y alertas: SweetAlert2
- Tablas: DataTables (jQuery plugin + adaptador Bootstrap 5)
- Base de datos: MySQL
- Auth: Laravel Breeze + Spatie Laravel Permission — **ya implementado, fuera de alcance**

---

## CDN (sin instalación vía npm/composer para frontend assets)

Todo el frontend se carga por CDN en el layout base (`resources/views/admin/layouts/app.blade.php`, ya existente). No usar `npm install` para estas librerías:

```html
<!-- Bootstrap 5 CSS -->
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
/>

<!-- Bootstrap Icons -->
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
/>

<!-- DataTables (core + Bootstrap 5 styling) -->
<link
    href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css"
    rel="stylesheet"
/>

<!-- SweetAlert2 -->
<link
    href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    rel="stylesheet"
/>

<!-- jQuery (solo requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap 5 JS (bundle incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Capa AJAX propia del proyecto, cargada al final, antes del JS de cada página -->
<script src="{{ asset('js/config/ajax.js') }}"></script>
```

### Configuración profesional de AJAX (`resources/js/config/ajax.js`)

Módulo único, cargado una sola vez en el layout, basado en `XMLHttpRequest` puro (sin `$.ajax`, sin `fetch`). Expone `window.ajax` con métodos que devuelven Promesas, maneja el token CSRF automáticamente y normaliza errores (422 se deja pasar para pintar en el formulario; el resto se muestra con SweetAlert2 vía un interceptor global):

```javascript
(function () {
    const baseURL = "/api";
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]',
    )?.content;

    function request(method, url, data = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open(method, baseURL + url, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.setRequestHeader("Accept", "application/json");
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken ?? "");

            const isFormData = data instanceof FormData;
            if (!isFormData) {
                xhr.setRequestHeader("Content-Type", "application/json");
            }

            xhr.onload = function () {
                let response;
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    response = null;
                }

                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(response);
                    return;
                }

                const error = { status: xhr.status, response };

                // Los errores de validación (422) se dejan pasar sin alerta global,
                // se pintan en el formulario correspondiente.
                if (xhr.status === 422) {
                    reject(error);
                    return;
                }

                // Interceptor global: cualquier otro error se muestra con SweetAlert2.
                Swal.fire({
                    icon: "error",
                    title: "Ocurrió un error",
                    text:
                        response?.message ??
                        "Intenta nuevamente en unos segundos.",
                });

                reject(error);
            };

            xhr.onerror = function () {
                const error = { status: 0, response: null };
                Swal.fire({
                    icon: "error",
                    title: "Sin conexión",
                    text: "No se pudo contactar al servidor. Verifica tu conexión.",
                });
                reject(error);
            };

            xhr.send(isFormData ? data : data ? JSON.stringify(data) : null);
        });
    }

    window.ajax = {
        get: (url) => request("GET", url),
        post: (url, data) => request("POST", url, data),
        put: (url, data) => request("PUT", url, data),
        delete: (url) => request("DELETE", url),
    };
})();
```

Uso en cada página (`resources/js/pages/pacientes.js`, etc.):

```javascript
// Crear (formulario propio, distinto al de edición)
ajax.post("/pacientes", formData)
    .then((res) => {
        Swal.fire("Listo", res.message, "success");
        table.ajax.reload();
        bootstrap.Modal.getInstance(
            document.getElementById("modalCrearPaciente"),
        ).hide();
    })
    .catch((error) => {
        if (error.status === 422) {
            pintarErroresValidacion(error.response.errors, "formCrearPaciente");
        }
    });

// Eliminar (siempre con confirmación SweetAlert2 antes del request)
function eliminarPaciente(id) {
    Swal.fire({
        icon: "warning",
        title: "¿Eliminar paciente?",
        text: "Esta acción no se puede deshacer.",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
    }).then((result) => {
        if (!result.isConfirmed) return;

        ajax.delete(`/pacientes/${id}`).then((res) => {
            Swal.fire("Eliminado", res.message, "success");
            table.ajax.reload();
        });
    });
}
```

---

## PREFERENCIAS GENERALES

- Todos los textos visibles y el código de comunicación del agente en español
- **Seeders 100% manuales con datos realistas de una clínica veterinaria** (nombres de mascotas, dueños, servicios, medicamentos, tipos de vacuna, horarios, especies y razas reales). **No usar `Faker` genérico para ningún dato base**: catálogos (`species`, `breeds`, `services`, `vaccine_types`, `medicines`), `branches`, ni el set inicial de `owners`/`pacientes`/`citas` que sirve para probar el flujo completo — todo eso se escribe a mano, curado, coherente entre sí (ej. una vacuna de perro no debería quedar asociada a un gato). `Faker` solo se permite, de forma opcional, si luego se necesita _volumen adicional_ de prueba (ej. generar 50 citas más) una vez que el set manual ya existe y funciona.
- Un seeder por modelo, en orden de dependencia, todos llamados desde `DatabaseSeeder.php`
- Para imágenes (foto de mascota, avatar de usuario): Cloudinary

---

## PREFERENCIAS DE DISEÑO

- **Plantilla base: Velzon (Bootstrap 5 admin template)** — el sidebar, topbar y la mayoría del maquetado HTML de las vistas **ya están creados** a partir de esa plantilla. No rediseñar ni recrear layout, sidebar, cards o estructura de páginas desde cero: se reutiliza el HTML de Velzon existente y solo se conecta a los datos reales (Blade + JavaScript).
- Diseño simple e intuitivo, pensado para personal no técnico (recepción, veterinarios)
- Mantener el estilo visual de Velzon tal como está — evitar CSS custom salvo casos excepcionales
- Iconos: los que trae Velzon (normalmente Remix Icon / Bootstrap Icons, revisar cuál usa la plantilla ya montada antes de agregar otra librería de iconos)
- El layout base (`admin/layouts/app.blade.php`, ya adaptado de Velzon) es el único punto donde se cargan los CDN — las vistas nunca cargan su propia copia de Bootstrap/jQuery/etc.
- **Crear y editar son pantallas/modales distintos**, cada uno con su propio partial Blade (ej. `partials/pacientes/modal-crear.blade.php` y `partials/pacientes/modal-editar.blade.php`), nunca un único modal que cambia de título y de endpoint según una variable JS.

### Orden de construcción por fase: API primero, Blade después

Para cada módulo de cada fase, el orden de trabajo es siempre:

1. Form Request (`Store*Request` y `Update*Request`, por separado)
2. Action (lógica de negocio + transacción si aplica)
3. Filters, si el listado necesita filtros
4. Controller `Api/Admin/*` + ruta en `routes/api.php`
5. Resource
6. Probar el endpoint de forma aislada (Tinker/Postman) antes de tocar la vista
7. Recién ahí: conectar la vista Blade ya existente (Velzon) con JavaScript — no se maqueta HTML nuevo, se cablea el que ya está, respetando formularios separados para crear/editar

Esto evita mezclar bugs de lógica de negocio con bugs de maquetado/JS en la misma sesión.

---

## PREFERENCIAS DE CÓDIGO

### General

- Código legible, simple, mínimo anidamiento
- Controladores delgados: nunca contienen queries ni reglas de negocio, solo orquestan Request → Action → Resource → JSON
- Si hay duda, revisar esta especificación antes de asumir

### Backend (Laravel)

- Form Requests para **todas** las validaciones y reglas de negocio (incluye unicidad, ej. `username` ya existe), con `Store*Request` y `Update*Request` separados
- Actions para toda la lógica de negocio (ver sección Arquitectura)
- Filtros de listados vía Pipeline (`app/Filters/`), nunca scopes
- `DB::transaction()` obligatorio en toda Action que escriba en 2+ tablas
- API Resources (`app/Http/Resources/`) para toda respuesta JSON que incluya modelos
- Un controlador Web (`Admin/`) y uno Api (`Api/Admin/`) por recurso — nunca mezclados
- Policies para control de acceso fino, usando lo ya existente de Spatie — no se crean roles/permisos nuevos
- Eloquent ORM, evitar queries en crudo salvo necesidad real
- Seeders manuales (ver Preferencias generales) + Factories con Faker solo para volumen de prueba
- Respuesta base unificada para las rutas AJAX (Api):

```json
{
    "status": true,
    "message": "Operación exitosa",
    "data": {},
    "errors": {}
}
```

### Frontend (Blade + JavaScript)

- Separar los scripts de cada vista en su propio archivo en `resources/js/pages/`
- `ajax.js` se carga una sola vez en el layout, expone `window.ajax` ya configurado con CSRF (ver sección CDN)
- Las peticiones AJAX de cada módulo usan `ajax.get/post/put/delete(...)`, nunca `$.ajax` ni `fetch` directo
- No mezclar lógica de negocio en el JS — el JS solo arma el request y pinta la respuesta
- Reutilizar partials Blade (`resources/views/partials/`) para elementos repetidos (modales, cards, empty states), manteniendo siempre modales/formularios separados entre crear y editar
- Toda acción destructiva pasa primero por confirmación SweetAlert2 antes de disparar el request

---

## ESTRUCTURA DE ARCHIVOS

```
app/
  Actions/
    Pacientes/
    Citas/
    Vacunas/
    Cirugias/
    Facturacion/
    ...
  Filters/
    Citas/
    Vacunas/
    Facturas/
    ...
  Http/
    Controllers/
      Admin/          # devuelven vistas Blade
      Api/
        Admin/        # devuelven JSON, consumidos vía AJAX
    Requests/
      Pacientes/
        StorePacienteRequest.php
        UpdatePacienteRequest.php
      Citas/
      ...
    Resources/
    Middleware/
  Models/              # ya creados — no regenerar
  Policies/
database/
  migrations/          # ya creadas
  seeders/             # manuales, uno por modelo
  factories/           # solo para volumen de prueba
resources/
  views/
    admin/
      layouts/         # ya existente (Velzon)
      dashboard.blade.php   # placeholder, no se toca
      pacientes/
      citas/
      owners/
      vacunas/
      surgiere/
      facturacion/
      usuarios/
    partials/
      pacientes/
        modal-crear.blade.php
        modal-editar.blade.php
      ...
  js/
    config/
      ajax.js
    pages/         # un archivo JS por vista/módulo del admin
routes/
  web.php          # navegación normal (vistas) → Controllers/Admin
  api.php          # acciones AJAX (guard api) → Controllers/Api/Admin
```

---

## FASES DE DESARROLLO

- Revisar el historial de git antes de explorar el repositorio
- Seguir las fases definidas arriba en orden exacto (2 a 8; la Fase 1 ya está resuelta), backend y frontend de cada módulo, **sin detenerse a pedir confirmación entre fases** — ejecución corrida hasta terminar todo el plan
- No hacer pruebas de usuario final por cuenta propia: solo verificar que el código compile/levante antes de pasar al siguiente módulo/fase
- No entrar en modo de replanificación ni rediseño del plan ya definido
- Solo detenerse a preguntar si hay un bloqueo técnico real (dependencia rota, conflicto de migración, endpoint faltante que bloquee una vista)
- No romper funcionalidades de fases anteriores
- No tocar nada de auth/permisos bajo ninguna circunstancia, aunque parezca relacionado a un módulo
- Fase 9 (Reportes) queda fuera de alcance hasta nueva indicación

---

## DOCUMENTACIÓN VIVA DEL PROYECTO (SDD)

Tres archivos en la raíz, obligatorios en cada fase:

- **`plan-implementacion.md`**: desglose de tareas por fase y módulo (orden de ejecución obligatorio: Form Request → Action → Filters → Controller Api/Admin → Resource → Controller Admin → vista Blade + JavaScript)
- **`tasks.md`**: checklist de cada tarea del plan con su estado (pendiente/en progreso/completada), se actualiza tarea por tarea
- **`project-map.md`**: estado real del sistema — módulos, rutas, Actions, Filters, Resources y vistas que ya existen, decisiones técnicas tomadas, impacto de cada fase. Es la fuente de verdad de "dónde quedó" el proyecto si el proceso se corta. Se actualiza **al terminar cada fase**, antes de pasar a la siguiente. Si hay discrepancia entre el código y este archivo, el código manda y el archivo se corrige.
- **`prompts.md`**: registro histórico de cada prompt ejecutado (build inicial, fixes posteriores), con fecha
- Los fixes puntuales post-build (UI, bugs) **no** se agregan como fase nueva a `plan-implementacion.md` — van en `fixes/fixes-01.md`, `fixes-02.md`, etc., y se documentan en `project-map.md` como entrada aparte tipo "Fix — [fecha]"

---

## OTRAS CONSIDERACIONES

- Zona horaria del sistema: `America/Lima`

---

## MODO IMPLEMENTACIÓN

- Solo código, mínimos comentarios — el código debe ser autoexplicativo
- No explicar qué hace el código en el chat salvo que se pida
- Responder en español
- Si hay ambigüedad, asumir la decisión más simple que no rompa nada
- Si algo no está claro en la especificación, preguntar antes de asumir
- Nunca modificar código que no esté relacionado con la tarea actual
- Nunca regenerar/reescribir los modelos Eloquent existentes salvo que falte algo puntual (relación, cast, fillable)
- Nunca tocar auth/permisos, bajo ninguna circunstancia
