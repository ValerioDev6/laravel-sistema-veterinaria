<?php

namespace App\Actions\VaccineTypes;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\VaccineType;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListVaccineTypesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(VaccineType::query()->with('species'))
            ->through([
                new FiltrarPorBusqueda($request, ['name']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}