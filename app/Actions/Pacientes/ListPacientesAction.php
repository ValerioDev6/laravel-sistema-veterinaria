<?php

namespace App\Actions\Pacientes;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListPacientesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Paciente::with(['owner', 'species', 'breed']))
            ->through([
                new FiltrarPorBusqueda($request, ['name']),
                new OrdenarPor($request, [0 => 'id', 1 => 'name'], [0, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}
