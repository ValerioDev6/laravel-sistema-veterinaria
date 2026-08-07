<?php

namespace App\Actions\Vacunas;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListVacunasAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Vacuna::with(['paciente', 'user', 'vaccine_type']))
            ->through([
                new FiltrarPorBusqueda($request, ['vaccination_date']),
                new OrdenarPor($request, [0 => 'id', 1 => 'vaccination_date'], [1, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}