## Fix #6 — Lógica de búsqueda/orden suelta en controllers (debe vivir en Actions + Filters)

**Tipo:** Fix de arquitectura — continuación directa del Fix #5, mismo criterio aplicado a todos los módulos

**Prompt:**

Antes de tocar nada, lee en este orden:

1. `proyecto-veterinaria.md` — fuente de verdad funcional del sistema
2. `plan-implementacion.md` — verifica en qué fase/módulo estamos
3. `project-map.md` — estado real, incluyendo lo que quedó del Fix #5 (Pipeline nativo + `List{Modulo}Action`)

**Problema encontrado:** el Fix #5 movió el listado a `List{Modulo}Action` solo en los módulos que ya tenían Filters formales (Citas). Pero hay controllers, como `OwnerController@index`, que **nunca pasaron por ese patrón** y siguen con lógica de negocio cruda dentro del método: armado manual de `where(...)->orWhere(...)` para búsqueda, y un mapeo a mano de columnas ordenables (`$orderable[...]`) con validación de dirección de orden. Eso es lógica, no debería vivir en el controller — misma regla que ya aplicamos, sin excepciones.

---

### Qué hacer

**Audita `app/Http/Controllers/Api/Admin/**` completo.** Para cada `index()` que tenga cualquiera de estas cosas escritas directo en el controller (no en una clase dedicada):

- Construcción de búsqueda (`where`/`orWhere` sobre columnas)
- Mapeo de columnas ordenables + validación de dirección (`asc`/`desc`)
- Cualquier otro `if`/lógica condicional sobre el query antes de paginar

...conviértelo al mismo patrón del Fix #5:

1. **Búsqueda y orden se vuelven Filters de Pipeline**, no si sueltos en el controller. Ejemplo para `Owners`:

```php
<?php

namespace App\Filters\Owners;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorBusqueda
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $search = trim((string) $this->request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $next($query);
    }
}
```

```php
<?php

namespace App\Filters\Owners;

use Closure;
use Illuminate\Http\Request;

class OrdenarPor
{
    protected array $orderable = [0 => 'id', 1 => 'first_name', 2 => 'last_name', 3 => 'email'];

    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $column = $this->orderable[(int) $this->request->input('sort_by', 1)] ?? $this->orderable[1];
        $dir = strtolower((string) $this->request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        return $next($query->orderBy($column, $dir));
    }
}
```

2. **La Action de lectura queda como única dueña de armar la query**, igual que `ListCitasAction`:

```php
<?php

namespace App\Actions\Owners;

use App\Filters\Owners\FiltrarPorBusqueda;
use App\Filters\Owners\OrdenarPor;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListOwnersAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Owner::withCount('pacientes'))
            ->through([
                FiltrarPorBusqueda::class,
                OrdenarPor::class,
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}
```

3. **El controller queda delgado**, sin ninguna variable intermedia de lógica:

```php
public function index(Request $request, ListOwnersAction $action): JsonResponse
{
    $paginated = $action->execute($request);

    return response()->json([
        'success' => true,
        'data' => OwnerResource::collection($paginated->items()),
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

**Nota sobre búsqueda y orden como Filters transversales:** si varios módulos repiten el mismo patrón de "buscar en N columnas" y "ordenar por columna mapeada", evalúa si conviene un Filter genérico reutilizable en vez de uno por módulo (ej. `App\Filters\Shared\FiltrarPorBusquedaGenerica` configurable con las columnas por constructor). Si el patrón varía poco entre módulos, hazlo genérico; si cada módulo tiene reglas de búsqueda distintas (columnas relacionadas, búsqueda en tabla ligada, etc.), mantenlo por módulo. Usa criterio, documenta la decisión que tomes.

---

### Alcance

- Audita **absolutamente todos** los `index()` en `app/Http/Controllers/Api/Admin/**`, no asumas que solo `Owners` tiene este problema — revisa uno por uno
- `app/Filters/**` — agregar los Filters de búsqueda/orden que falten por módulo
- `app/Actions/**` — agregar/completar el `List{Modulo}Action` correspondiente donde falte
- No toques Blade
- El JS de DataTables no debería necesitar cambios si el envelope de respuesta (`success/data/pagination`) se mantiene igual — verifícalo, no lo asumas

---

**Al terminar:**

Crea `/fixes/2026-08-06-logica-suelta-controllers.md` documentando:

- Tabla: módulo → tenía lógica suelta (sí/no) → qué se movió (Filters creados + Action creada)
- Confirmación de que el envelope de respuesta no cambió para ningún módulo (o si cambió algo, por qué)
- Fecha del fix

Actualiza `project-map.md`: tablas "Filters registrados" y "Actions registradas" con todo lo nuevo.

Registra este mismo prompt en `prompts.md` como el siguiente número de entrada correlativo, con la fecha de hoy.

Detente al terminar y espera confirmación antes de continuar con cualquier otra cosa.
