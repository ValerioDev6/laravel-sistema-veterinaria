<?php

namespace App\Actions\Cirugias;

use App\Filters\Cirugias\FiltrarPorBusquedaCirugia;
use App\Filters\Cirugias\FiltrarPorEspecieCirugia;
use App\Filters\Cirugias\FiltrarPorEstadoPagoCirugia;
use App\Filters\Cirugias\FiltrarPorFechaCirugia;
use App\Filters\Cirugias\FiltrarPorVeterinarioCirugia;
use App\Filters\Shared\OrdenarPor;
use App\Models\Invoice;
use App\Models\Surgiere;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListCirugiasAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Surgiere::with([
                "paciente",
                "user",
                "invoice",
                "invoice.payments",
            ]))
            ->through([
                new FiltrarPorBusquedaCirugia($request),
                new FiltrarPorEspecieCirugia($request),
                new FiltrarPorVeterinarioCirugia($request),
                new FiltrarPorEstadoPagoCirugia($request),
                new FiltrarPorFechaCirugia($request),
                new OrdenarPor($request, [0 => "id", 1 => "surgery_date"], [1, "desc"]),
            ])
            ->thenReturn();

        return $query->paginate($request->integer("per_page", 15));
    }
}
