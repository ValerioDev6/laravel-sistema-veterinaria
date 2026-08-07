## Fix #4 — DataTables roto por la paginación del backend (migrar a server-side real)

**Tipo:** Fix de arquitectura, alcance amplio (toca Blade + JS + API en casi todos los módulos)

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido

**Contexto del bug:** el Fix retroactivo de paginación (`->paginate(15)` en los `index()` de Api/Admin) rompió los listados de DataTables. Causa raíz: DataTables estaba configurado en modo **client-side** (`serverSide: false` o sin especificar), lo cual espera recibir **todo el dataset completo** en una sola respuesta para paginar él mismo en el navegador. Al recibir solo 15 registros por página desde el backend, DataTables interpreta que esos 15 son el total de datos que existen y no puede navegar más allá.

**Decisión de fix:** migrar a **DataTables server-side real** (`serverSide: true`) en todos los módulos afectados, en vez de revertir la paginación del backend. Es la solución que escala correctamente y evita traer ddatasets completos al navegador.

---

### Alcance (toca ambas capas, a diferencia de fixes anteriores)

- `app/Http/Controllers/Api/Admin/**` — todos los `index()` que alimentan una tabla en el admin
- `resources/js/pages/*.js` — configuración de DataTables de cada módulo
- **No toques** `routes/web.php`, Actions, ni el módulo de autenticación

### Parte 1 — Backend: adaptar `index()` al contrato de DataTables server-side

DataTables server-side espera un formato de respuesta específico quer NO es tu envelope unificado (`status/message/data/errors`). Para los endpoints que alimentan una tabla, la respuesta debe ser:

```json
{
  "draw": 1,
  "recordsTotal": 120,
  "recordsFiltered": 45,
  "data": [ ... ]
}
```

Para cada `index()` afectado:

- Lee los parámetros que manda DataTables: `draw`, `start`, `length`, `search[value]`, `order[0][column]`, `order[0][dir]`
- Aplica los Filters existentes (Pipeline) para `search[value]` y cualquier filtro de columna que ya tuvieras
- Usa `skip($start)->take($length)` (o `paginate` calculando la página a partir de `start`/`length`) para el corte real de datos
- `recordsTotal`: total sin filtrar. `recordsFiltered`: total después de aplicar filtros/búsqueda
- `data`: el array de recursos (via Resource) para esa página únicamente

**Mantén el envelope unificado (`status/message/data/errors`) sin cambios en todos los demás métodos** (`store`, `update`, `destroy`, `show`) — este cambio de contrato aplica _solo_ a los `index()` que DataTables consume directamente.

### Parte 2 — Frontend: configurar DataTables server-side en cada módulo

En cada `resources/js/pages/{modulo}.js` que inicializa una tabla:

```js
$("#tabla-{modulo}").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "/api/admin/{modulo}",
        headers: { "X-CSRF-TOKEN": token },
        // usar la instancia de Axios ya configurada del proyecto, no fetch suelto
    },
    columns: [/* mapear a los campos reales del Resource */],
});
```

Revisa cada módulo ya construido (Branches, Users, Species, Breeds, Services, VaccineTypes, Medicines, Owners, Pacientes, VeterinarianSchedules, Citas, Vacunas, Cirugías, MedicalRecords) y aplica el mismo cambio de forma consistente — mismo patrón en todos, no una solución distinta por módulo.

---

**Al terminar:**

Crea `/fixes/2026-08-06-datatables-serverside.md` documentando:

- Causa raíz exacta del quiebre
- Lista de módulos migrados a server-side (todos deberían quedar iguales)
- Confirmación de que el envelope unificado se mantuvo intacto en store/update/destroy/show
- Fecha del fix

Actualiza la tabla "Fixes aplicados" en `project-map.md`, y agrega una nota en "Decisiones técnicas" explicando que los `index()` que alimentan DataTables usan el contrato server-side de DataTables en vez del envelope unificado, con la justificación de por qué (evitar traer datasets completos al navegador).

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

Prueba manualmente (o deja instrucciones claras de cómo probar) que la navegación entre páginas, la búsqueda y el ordenamiento funcionan en al menos 2-3 módulos representativos antes de dar el fix por cerrado. Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
