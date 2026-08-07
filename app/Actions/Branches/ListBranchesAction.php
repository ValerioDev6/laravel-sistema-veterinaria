<?php

namespace App\Actions\Branches;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Branch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\Request;

class ListBranchesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Branch::query())
            ->through([
                new FiltrarPorBusqueda($request, ['name', 'address', 'city', 'phone']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name', 2 => 'address', 3 => 'city', 4 => 'phone'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}
