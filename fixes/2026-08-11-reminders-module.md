# Fase 8 — Módulo Recordatorios (Reminders)

**Fecha:** 2026-08-11
**Estado:** Completado — última fase pendiente del proyecto (`project-map.md`)

## Contexto

Inventario previo: `Reminder` era el único modelo de dominio sin uso real — solo se creaba como efecto secundario de `CreateVacunaAction` (tipo `vacuna`) y nunca se leía. La tabla `reminders` existía en BD con **0 filas** y sin migración propia. El usuario pidió implementar el módulo final con el mismo formato de tabla del resto del sistema.

## Qué se construyó

| Capa | Archivo |
|------|---------|
| Form Request | `app/Http/Requests/Reminders/UpdateReminderRequest.php` (`status` in pendiente/enviado/cancelado) |
| Actions | `app/Actions/Reminders/CambiarEstadoReminderAction.php`, `ListRemindersAction.php` |
| Filters | `app/Filters/Reminders/{FiltrarPorBusquedaReminders,FiltrarPorTipoReminder,FiltrarPorPetReminder,FiltrarPorEstadoReminder}.php` |
| Resource | `app/Http/Resources/ReminderResource.php` (`type_label`, paciente, species, owner_name) |
| Controller Api | `app/Http/Controllers/Api/Admin/ReminderController.php` (index, cambiarEstado) |
| Controller Admin | `app/Http/Controllers/Admin/ReminderController.php` (index con pacientes para filtro) |
| Vistas | `resources/views/admin/reminders/index.blade.php` |
| JS | `public/js/pages/reminders.js` |
| Seeder | `database/seeders/ReminderSeeder.php` (+ agregado al pipeline de `DatabaseSeeder`) |
| Rutas | web `admin.reminders.index`; API `admin.api.reminders.index` + PATCH `admin.api.reminders.estado` |
| Sidebar | Entrada "Recordatorios" → `admin.reminders.index` (sección Clínica) |

## Decisiones

- **Búsqueda**: cubre el texto del reminder (`message`), el tipo (`remindable_type`) y el nombre de la mascota vía `whereHas('paciente')` — cumple el requisito de "considera su texto" y de la relación con Paciente.
- **Filtros dedicados**: `tipo` (cita/vacuna/cirugia), `pet_id` (mascota) y `status` (pendiente/enviado/cancelado); orden por defecto `remind_at` asc (próximos primero).
- **`type_label()`**: reutiliza el match de `InvoiceResource` (`cita`→Cita, `vacuna`→Vacuna, `cirugia`/`surgiere`→Cirugía) para compatibilidad con ambos valores de tipo polimórfico.
- **Cambio de estado**: PATCH `reminders/{reminder}/estado` con botones por fila (marcar enviado / cancelado / volver a pendiente). Se permite regresar a `pendiente` desde cualquier estado (la Action no restringe transiciones).
- **Seeder demo idempotente**: los seeders insertan directo (por eso no pasan por Actions, igual que el resto). Genera 11 reminders: 6 de citas (confirmadas/pendientes), 1 de cirugía en proceso, 4 de vacunas con `next_due_date`, estados pendiente/enviado.

## Verificación

- `php -l` en los 14 archivos PHP modificados/creados: OK. `node --check reminders.js`: OK.
- `view:clear` + `view:cache`: OK.
- Seeder ejecutado: 11 reminders con los 3 tipos.
- `GET /admin/reminders` (sesión): 200.
- API: `search=cirug`→2, `search=Rocky`→2 (mascota), `tipo=vacuna`→4, `status=enviado`→2, `pet_id=1`→2, `search=Próxima dosis`→4.
- `PATCH reminders/1/estado` {status: enviado}→200; {status: xyz}→422.

## Registro

- `prompts.md` → Prompt #27.
- `project-map.md` → Fase 8 ✅ 2026-08-11 (módulo, rutas, actions, filters, resources, vistas, decisiones, fix).
- `proyecto-veterinaria.md` → Fase 8 marcada implementada.
- `tasks.md` → 8.1.x completadas.
