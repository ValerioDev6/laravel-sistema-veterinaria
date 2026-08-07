## Fix #5 — Eliminar Pipeline y "DataTables" custom, usar Pipeline + paginación nativa de Laravel

**Tipo:** Fix de arquitectura — refactor de plumbing, sin tocar reglas de negocio existentes

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real de lo ya construido

**Este fix es de refactor, no de funcionalidad.** No elimines ni alteres ninguna regla de negocio existente (ej. la validación de conflicto de horario en `CreateCitaAction`, las validaciones de dependencias antes de eliminar en Owners/Species/etc.) — solo se reemplaza el mecanismo interno de filtros y de respuesta paginada por el patrón nativo de Laravel.

---

### Problema encontrado

Se crearon dos clases custom que no debían existir:

1. **`App\Filters\Pipeline`** — un wrapper propio alrededor de lo que ya resuelve `Illuminate\Pipeline\Pipeline` de forma nativa. Los Filters actuales (`FiltrarPorVeterinario`, `FiltrarPorFecha`, `FiltrarPorEstado`, etc.) usan `static function handle(Builder $query, Request $request): Builder`, que **no es el contrato real del Pipeline de Laravel** — es una firma inventada que imita el patrón pero no lo es.

2. **`App\Filters\DataTables`** (con método `::server()`) — una clase custom para formatear la respuesta server-side. El plugin DataTables.net (cargado por CDN, ya con soporte server-side incluido) solo necesita recibir el JSON estándar (`draw`, `recordsTotal`, `recordsFiltered`, `data`) — no hace falta una clase PHP dedicada a esto, y menos con un nombre que choca con el de la librería JS.

---

### Parte 1 — Reemplazar `App\Filters\Pipeline` por el Pipeline nativo

Elimina `App\Filters\Pipeline` por completo.

Convierte **cada Filter existente** (busca todos los que están en `app/Filters/**`) al contrato real del Pipeline de Laravel — instancia de clase, no estático, con `handle($passable, Closure $next)`:

```php
<?php

namespace App\Filters\Citas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorVeterinario
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled('veterinarian_id')) {
            $query->where('veterinarian_id', $this->request->integer('veterinarian_id'));
        }

        return $next($query);
    }
}
```

En cada Controller, reemplaza el uso de `Pipeline::apply(...)` por el Pipeline real de Laravel:

```php
use Illuminate\Pipeline\Pipeline;

$query = app(Pipeline::class)
    ->send(Cita::with(['paciente', 'veterinarian', 'service']))
    ->through([
        FiltrarPorVeterinario::class,
        FiltrarPorFecha::class,
        FiltrarPorEstado::class,
    ])
    ->thenReturn();
```

Aplica este mismo cambio en **todos** los módulos que ya tengan Filters (revisa el resultado del Fix #3 en `project-map.md` para saber cuáles son).

### Parte 2 — Eliminar `App\Filters\DataTables`, usar paginación nativa de Laravel

Elimina `App\Filters\DataTables` por completo. No se reemplaza por otra clase — Laravel ya resuelve el conteo y el corte de datos con `->paginate()`, no hay que reimplementar `skip()`/`take()`/`getCountForPagination()` a mano.

Cada `index()` de Api/Admin que alimenta una tabla usa `->paginate()` directo y devuelve un envelope limpio, agnóstico de DataTables (reutilizable por cualquier consumidor, no solo el admin):

```php
public function index(Request $request): JsonResponse
{
    $query = app(Pipeline::class)
        ->send(Cita::with(['paciente', 'veterinarian', 'service']))
        ->through([
            FiltrarPorVeterinario::class,
            FiltrarPorFecha::class,
            FiltrarPorEstado::class,
        ])
        ->thenReturn();

    $paginated = $query->paginate($request->integer('per_page', 15));

    return response()->json([
        'success' => true,
        'data' => CitaResource::collection($paginated->items()),
        'pagination' => [
            'total' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'has_more' => $paginated->currentPage() < $paginated->lastPage(),
        ],
    ]);
}
```

**El backend no conoce DataTables ni su contrato (`draw`/`recordsTotal`/`recordsFiltered`)** — eso es responsabilidad exclusiva del JS. En cada `resources/js/pages/{modulo}.js`, configura DataTables con `ajax` como **función** (no URL directa), para traducir la respuesta limpia del backend al formato interno que DataTables server-side necesita:

```js
$("#tabla-citas").DataTable({
    processing: true,
    serverSide: true,
    ajax: function (data, callback) {
        const page = Math.floor(data.start / data.length) + 1;

        axios
            .get("/api/admin/citas", {
                params: {
                    per_page: data.length,
                    page: page,
                    search: data.search.value,
                },
            })
            .then((response) => {
                callback({
                    draw: data.draw,
                    recordsTotal: response.data.pagination.total,
                    recordsFiltered: response.data.pagination.total,
                    data: response.data.data,
                });
            });
    },
    columns: [/* mapear a los campos reales del Resource */],
});
```

Aplica este mismo patrón (backend con `paginate()` + envelope `success/data/pagination`, JS con función `ajax` que traduce) en **todos** los módulos, de forma consistente.

---

### Alcance

- Audita **toda** la carpeta `app/Http/Controllers/Api/Admin/**` — todos los endpoints JSON de la API, no solo Citas
- `app/Filters/**` — convertir todos los Filters existentes
- Eliminar `App\Filters\Pipeline` y `App\Filters\DataTables`
- No toques Blade ni JS en este fix (el JS de DataTables ya consume el JSON, no cambia nada de su lado si el contrato de respuesta se mantiene igual)
- No toques Actions ni reglas de negocio — solo el mecanismo de filtrado y de respuesta paginada

---

**Al terminar:**

Crea `/fixes/2026-08-06-pipeline-nativo-y-datatables-response.md` documentando:

- Confirmación de que `App\Filters\Pipeline` y `App\Filters\DataTables` fueron eliminadas
- Lista de módulos migrados al Pipeline nativo
- Confirmación de que ninguna regla de negocio existente cambió de comportamiento
- Fecha del fix

Actualiza `project-map.md`: la tabla "Filters registrados" debe reflejar el nuevo contrato (`handle($passable, Closure $next)`), y agrega la decisión técnica de por qué se usa el Pipeline nativo en vez de uno propio.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
