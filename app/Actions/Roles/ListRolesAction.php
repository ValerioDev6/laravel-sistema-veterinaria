<?php

namespace App\Actions\Roles;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Spatie\Permission\Models\Role;

class ListRolesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Role::query()->withCount(["users", "permissions"]))
            ->through([
                new FiltrarPorBusqueda($request, ["name"]),
                new OrdenarPor($request, [0 => "name", 1 => "guard_name"], [0, "asc"]),
            ])
            ->thenReturn();

        return $query->paginate($request->integer("per_page", 15));
    }
}
