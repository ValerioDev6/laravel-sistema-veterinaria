## Fix #1 — Sidebar: inconsistencia de UI (tamaño de fuente + menús redundantes)

**Tipo:** Fix de UI, sin nueva funcionalidad

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, vistas, módulos)

No implementes nada de fases pendientes. Este es un fix puntual de UI sobre lo que ya existe.

**Problema a corregir:**

Revisa `resources/views/layouts/app.blade.php` (sidebar de Velzon) y todas las vistas `admin/**/index.blade.php` ya construidas. Hay dos problemas de inconsistencia visual:

1. **Tamaño de fuente inconsistente**: el sidebar está usando clases de Bootstrap puro en vez de las clases propias del tema Velzon, lo que hace que la tipografía del menú se vea desproporcionadamente grande comparada con el resto del layout (topbar, contenido). Identifica dónde se rompió la convención de clases de Velzon (probablemente se mezclaron clases genéricas de Bootstrap `fs-*`/tamaños custom en vez de las utilidades propias del tema) y corrígelo para que el sidebar respete la tipografía original de la plantilla Velzon en todo el árbol de menús.

2. **Menús redundantes / inconsistencia de flujo**: en varios módulos (ejemplo confirmado: Pacientes) el sidebar tiene un submenú "Crear [recurso]" que navega directo al formulario de creación, **mientras que la vista de listado (`index.blade.php`) ya tiene su propio botón "Nuevo/Crear [recurso]"** que hace lo mismo. Esto es un punto de entrada duplicado e inconsistente. Revisa **todos** los módulos ya construidos (Branches, Users, Species, Breeds, Services, VaccineTypes, Medicines, Owners, Pacientes, VeterinarianSchedules, Citas, Vacunas, Cirugías, MedicalRecords) y aplica el mismo criterio en todos:
    - El sidebar debe tener **un solo enlace por módulo**, apuntando al listado (`index`)
    - El botón de "Crear" vive únicamente en la vista de listado, no en el sidebar
    - Elimina del sidebar cualquier submenú "Crear X" / "Nuevo X" que sea redundante con el botón que ya existe en su `index.blade.php`

**Alcance:** Solo este fix. No apliques cambios de funcionalidad, no avances fases, no toques Actions/Controllers/Requests — esto es exclusivamente `app.blade.php` (sidebar) y, si hace falta ajustar alguna clase puntual, las vistas `index.blade.php` afectadas.

**Al terminar:**

Crea un archivo en `/fixes/` con el nombre `2026-08-06-sidebar-ui-inconsistencia.md` documentando:

- Qué se encontró (causa del tamaño de fuente incorrecto, lista de módulos con submenú redundante)
- Qué se corrigió exactamente (diff conceptual, no hace falta pegar el código completo)
- Fecha del fix

Actualiza también la tabla "Fixes aplicados" en `project-map.md` con una fila resumen apuntando a ese archivo.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

No hagas nada más allá de este fix. Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
