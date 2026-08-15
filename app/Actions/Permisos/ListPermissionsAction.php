<?php

namespace App\Actions\Permisos;

use App\Filters\Permisos\FiltrarPorGrupoPermiso;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Spatie\Permission\Models\Permission;

class ListPermissionsAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Permission::query()->withCount("roles"))
            ->through([
                new FiltrarPorBusqueda($request, ["name"]),
                new FiltrarPorGrupoPermiso($request),
                new OrdenarPor($request, [0 => "name", 1 => "guard_name"], [0, "asc"]),
            ])
            ->thenReturn();

        return $query->paginate($request->integer("per_page", 15));
    }
}
