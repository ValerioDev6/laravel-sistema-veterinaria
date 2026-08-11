<?php

namespace App\Actions\Vacunas;

use App\Filters\Shared\OrdenarPor;
use App\Filters\Vacunas\FiltrarPorBusquedaVacunas;
use App\Filters\Vacunas\FiltrarPorEspecieVacuna;
use App\Filters\Vacunas\FiltrarPorEstadoPagoVacuna;
use App\Filters\Vacunas\FiltrarPorFechaVacuna;
use App\Filters\Vacunas\FiltrarPorVeterinarioVacuna;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListVacunasAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Vacuna::with([
                "paciente",
                "user",
                "vaccine_type",
                "invoice",
                "invoice.payments",
            ]))
            ->through([
                new FiltrarPorBusquedaVacunas($request),
                new FiltrarPorEspecieVacuna($request),
                new FiltrarPorVeterinarioVacuna($request),
                new FiltrarPorEstadoPagoVacuna($request),
                new FiltrarPorFechaVacuna($request),
                new OrdenarPor($request, [0 => "id", 1 => "vaccination_date"], [1, "desc"]),
            ])
            ->thenReturn();

        return $query->paginate($request->integer("per_page", 15));
    }
}