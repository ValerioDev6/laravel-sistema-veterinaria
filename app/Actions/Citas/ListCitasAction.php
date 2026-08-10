<?php

namespace App\Actions\Citas;

use App\Filters\Citas\FiltrarPorBusquedaCitas;
use App\Filters\Citas\FiltrarPorEspecie;
use App\Filters\Citas\FiltrarPorEstado;
use App\Filters\Citas\FiltrarPorFecha;
use App\Filters\Citas\FiltrarPorVeterinario;
use App\Filters\Shared\OrdenarPor;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListCitasAction
{
    // Filtros: nombre de la mascota, veterinario, especie, estado, fecha (desde/hasta)

    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Cita::with(["paciente.species", "veterinarian", "service"]))
            ->through([
                new FiltrarPorVeterinario($request),
                new FiltrarPorEspecie($request),
                new FiltrarPorEstado($request),
                new FiltrarPorFecha($request),
                new FiltrarPorBusquedaCitas($request),
                new OrdenarPor(
                    $request,
                    [0 => 'id', 4 => 'appointment_date', 5 => 'appointment_time', 6 => 'status'],
                    [4, 'desc'],
                ),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}
