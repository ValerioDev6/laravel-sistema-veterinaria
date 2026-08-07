# Fix #2 — Sidebar: inconsistencia de UI (tamaño de fuente + menús redundantes)

**Tipo:** Fix de UI, sin nueva funcionalidad

**Fecha:** 2026-08-06

**Prompt de referencia:** `fixes/01_fix.md`

---

## Qué se encontró

Revisando `resources/views/layouts/app.blade.php` (sidebar de Velzon) y todas las vistas `admin/**/index.blade.php`:

### 1. Tamaño de fuente inconsistente
La mayoría de módulos simples (Branches, Users, Species, Breeds, Services, VaccineTypes, Owners, Pacientes, Medicines, Vacunas, Cirugías, Facturas, Medicamentos, Sucursales) estaban anidados dentro de un menú colapsable de Velzon (`menu-dropdown` con `ul.nav.nav-sm.flex-column`). Ese anidamiento usa la utilidad `nav-sm` del tema, cuyo tamaño de fuente (`--vz-vertical-menu-sub-item-font-size`) es menor que el de los ítems de nivel superior (`--vz-vertical-menu-item-font-size`), generando una mezcla de tipografías inconsistente: los ítems con submenú se veían proporcionalmente pequeños/anidados frente a los ítems top-level de la plantilla.

No se encontraron clases Bootstrap puras sueltas (`fs-*`) en el árbol de menús; la inconsistencia venía de **abrir submenú para módulos que en realidad son un simple enlace a su listado**, rompiendo el patrón tipográfico top-level de Velzon.

### 2. Menús redundantes
Confirmado en **todos** los módulos ya construidos: el `index.blade.php` de cada módulo ya tiene su propio botón "Nuevo/Crear" (entrada con `route('admin.X.create')`), pero el sidebar duplicaba ese punto de entrada con un subenlace "Nuevo/Crear X" / "Registrar X", además del "Listado".

Módulos con subenlace de crear redundante detectados:
- Branches ("Nueva Sucursal")
- Users ("Nuevo Usuario")
- Owners ("Nuevo Propietario")
- Pacientes ("Nuevo Paciente" + "Ficha del Paciente" apuntando a `#`)
- Services ("Nuevo Servicio")
- VaccineTypes (sin sub en sidebar propio, pero dentro de Vacunas)
- Medicines ("Nuevo Medicamento")
- Citas ("Nueva Cita")
- Vacunas ("Registrar Vacuna" + "Tipos de Vacuna")
- Cirugías ("Registrar Cirugía")
- Invoices ("Nueva Factura")

---

## Qué se corrigió exactamente

Todo en `resources/views/layouts/app.blade.php` (sidebar), sin tocar más nada.

**Removed submenucers rellenados y convertidos a enlace top-level simple** (un solo enlace por módulo → `index`):

- **Propietarios** → ahora `nav-link menu-link` directo a `admin.owners.index` (se eliminó subdropdown Listado / Nuevo Propietario).
- **Pacientes** → `nav-link menu-link` directo a `admin.pacientes.index` (se eliminaron Listado / Nuevo Paciente / la "Ficha del Paciente" muerta con `href="#"`).
- **Cirugías** → `nav-link menu-link` directo a `admin.cirugias.index` (se eliminaron Listado / Registrar Cirugía).
- **Facturas** → `nav-link menu-link` directo a `admin.invoices.index` (se eliminó "Nueva Factura"). El enlace "Pagos" se mantiene como ítem separado (es flujo distinto) y se normalizó a `nav-link menu-link`.
- **Servicios** → `nav-link menu-link` directo a `admin.services.index` (se eliminó Nuevo Servicio).
- **Medicamentos** → `nav-link menu-link` directo a `admin.medicines.index` (se elimino Nuevo Medicamento).
- **Sucursales** → `nav-link menu-link` directo a `admin.branches.index`.
- **Personal** → `nav-link menu-link` directo a `admin.usuarios.index` (se eliminaron Listado / Nuevo Usuario).
- **Historial Clínico** → normalizado a `nav-link menu-link`.

**Menus que se MANTUVIERON como submenú de Velzon** porque agrupan más de un listado real de distinto recurso (cant from eliminar por ser navegación legitima, no "crear"):
- **Citas**: conserva Listado + Calendario (se eliminó solo "Nueva Cita").
- **Vacunas**: conserva Listado + Tipos de Vacuna (se elimino solo "Registrar Vacuna").
- **Especies y Razas**: conserva su submenu con los dos índices (Species, Breeds) — no había sub "crear", solo son dos listados.

Como resultado, el árbol de menús ahora usa uniformemente la tipografía top-level de Velzon (`nav-link menu-link` con `--vz-vertical-menu-item-font-size`) en todo el sidebar, y cada módulo tiene **un único enlace** que apunta a su listado, dejando el botón "Crear" únicamente donde corresponde: dentro de su `index.blade.php` (verificado que las 15 vistas index construidas lo tienen).

---

## Motores de cambio
- Eliminados los bloques colapsables `menu-dropdown` redundantes del sidebar.
- Unificado el uso de la clase `nav-link menu-link` en todos los ítems top-level.
- Ajustados solo los elementos del sidebar; no se tocaron Actions/Controllers/Requests, ni se avanzaron fases pendientes.

## Estado de la carga de vistas
- `php artisan view:cache` pasado con éxito (Blade OK).