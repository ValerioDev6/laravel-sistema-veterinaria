## Fix #2 — Error en Medical Records index + rutas de Invoices/Pagos inconsistentes

**Tipo:** Fix de bug + fix de UI/routing, sin nueva funcionalidad

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido (rutas, vistas, módulos)

No implementes nada de fases pendientes. Estos son dos fixes puntuales.

---

### Problema 1 — Error en `resources/views/admin/medical-records/index.blade.php:5`

La vista tira `ErrorException` en la línea 5. Investiga la causa real antes de parchar a ciegas:

- Revisa `app/Http/Controllers/Admin/MedicalRecordController@index` — confirma qué variable(s) está pasando a la vista y compáralas con lo que la vista espera en la línea 5 y alrededores
- Causas típicas a descartar: variable no definida/no pasada desde el controller, relación no cargada con `with()` que la vista intenta acceder (`->paciente->name`, etc.), o un `foreach`/acceso a colección que llega `null` cuando no hay registros (falta el empty state)
- Corrige la causa raíz, no solo silencies el error con `??` sin entender por qué falta el dato
- Verifica que el empty state siga funcionando cuando no hay historiales médicos

---

### Problema 2 — `/admin/invoices` vs `/admin/invoices#pagos`

Aclara y deja consistente el flujo de Facturación y Pagos. Ahora mismo hay dos entradas de menú/enlaces que aparentan ser rutas distintas, pero `#pagos` es un fragmento de URL (ancla), **no genera una ruta ni un controlador distinto** — si ambos enlaces apuntan al mismo controller/vista, están cargando exactamente el mismo contenido completo sin diferenciar nada, lo cual confunde al usuario (parece un link roto o un submenú que no lleva a ningún lado real).

Decide y deja implementado **uno de estos dos patrones**, el que sea consistente con cómo ya armaste Pacientes (vista única con tabs vía AJAX):

**Opción recomendada — una sola vista `admin/invoices/index.blade.php` con tabs Bootstrap:**

- Un tab "Facturas" (listado de `invoices`) y un tab "Pagos" (listado de `payments`), usando el componente de tabs nativo de Velzon
- Al cargar la página, si la URL trae `#pagos`, el JS debe activar ese tab automáticamente (leer `location.hash` en el `pages/invoices.js` y disparar `data-bs-toggle="tab"` correspondiente)
- Una sola ruta `admin.invoices.index`, un solo entry en el sidebar
- Elimina cualquier segundo entry de sidebar que apunte a `/admin/invoices#pagos` como si fuera una página aparte — el hash se maneja solo con JS, no con un segundo `<a href>` de sidebar

Si por el contrario ya decidiste que Pagos merece su propia página real (`/admin/invoices/payments` con su propio controller/vista), entonces:

- Crea la ruta real `admin.invoices.payments` (no un ancla)
- Actualiza el sidebar para que apunte a esa ruta real, no a `#pagos`

Elige la opción que menos código duplicado genere dado lo que ya existe de Fase 7 (revisa `project-map.md` para ver qué tanto de Invoices/Payments ya está construido antes de decidir).

---

**Alcance:** Solo estos dos fixes. No avances fases, no toques módulos no relacionados.

**Al terminar:**

Crea `/fixes/2026-08-06-medical-records-error-e-invoices-rutas.md` documentando:

- Causa raíz del error en medical-records y qué se corrigió
- Qué opción se eligió para Invoices/Pagos y por qué
- Fecha del fix

Actualiza la tabla "Fixes aplicados" en `project-map.md`.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

No hagas nada más allá de estos dos fixes. Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
