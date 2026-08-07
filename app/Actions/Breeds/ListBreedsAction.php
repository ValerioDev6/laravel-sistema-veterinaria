<?php

namespace App\Actions\Breeds;

use App\Filters\Breeds\FiltrarPorSpecies;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListBreedsAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Breed::query()->with('species'))
            ->through([
                new FiltrarPorSpecies($request),
                new FiltrarPorBusqueda($request, ['name']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}