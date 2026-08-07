<?php

namespace App\Actions\Owners;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
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
                new FiltrarPorBusqueda($request, ['first_name', 'last_name', 'email', 'phone']),
                new OrdenarPor($request, [0 => 'id', 1 => 'first_name', 2 => 'last_name', 3 => 'email'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}