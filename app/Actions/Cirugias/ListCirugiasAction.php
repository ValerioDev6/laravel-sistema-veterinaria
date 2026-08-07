<?php

namespace App\Actions\Cirugias;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Surgiere;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListCirugiasAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Surgiere::with(['paciente', 'user']))
            ->through([
                new FiltrarPorBusqueda($request, ['surgery_date']),
                new OrdenarPor($request, [0 => 'id', 1 => 'surgery_date'], [1, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}