# Prompts — Sistema Veterinaria

> Registro histórico de cada prompt ejecutado, con fecha.

---

## Prompt #1 — 2026-08-06

**Tipo:** Planificación inicial

**Prompt:**

Lee `proyecto-veterinaria.md` completo (fuente de verdad funcional) y el dump de base de datos ya provisto (20 tablas, modelos ya creados).

Vas a generar `plan-implementacion.md` con el desglose de TODAS las fases (1 a 8, la Fase 9 de reportes queda fuera por ahora) en tareas granulares. Para cada módulo de cada fase, la tarea se desglosa en el orden de construcción definido en la spec (API primero, Blade después):

1. Form Request
2. Action (+ transacción si escribe en 2+ tablas)
3. Filters, si el listado necesita filtros
4. Controller Api/Admin/* + ruta en routes/api.php
5. Resource
6. Controller Admin/* + ruta en routes/web.php
7. Vista Blade (reutilizando el HTML de Velzon ya existente) + JS en resources/js/pages/ consumiendo con Axios

No implementes nada todavía — solo genera el plan.

Además, crea:
- `tasks.md`: checklist con cada tarea del plan, estado "pendiente"
- `project-map.md`: documento inicial vacío con la estructura que vas a ir llenando fase por fase (módulos, rutas, Actions, Filters, Resources, vistas existentes)
- `prompts.md`: registra este mismo prompt como primera entrada, con fecha de hoy

Al terminar, muéstrame el plan completo para que lo revise antes de que empieces a construir.

**Resultado:** Se generaron los 4 archivos de documentación viva del proyecto.
