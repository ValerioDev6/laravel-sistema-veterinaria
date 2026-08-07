<?php

namespace App\Actions\Species;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Species;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\Request;

class ListSpeciesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Species::query())
            ->through([
                new FiltrarPorBusqueda($request, ['name']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}
