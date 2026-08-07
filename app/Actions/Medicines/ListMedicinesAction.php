<?php

namespace App\Actions\Medicines;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListMedicinesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Medicine::query())
            ->through([
                new FiltrarPorBusqueda($request, ['name']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name', 2 => 'quantity'], [1, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}