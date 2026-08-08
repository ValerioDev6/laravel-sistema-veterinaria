<?php

namespace App\Actions\Citas;

use App\Filters\Citas\FiltrarPorEstado;
use App\Filters\Citas\FiltrarPorFecha;
use App\Filters\Citas\FiltrarPorVeterinario;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListCitasAction
{
    // filtas por nombr mascota , especio, veterinarian, estado de pago , fecha

    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Cita::with(['paciente', 'veterinarian', 'service']))
            ->through([
                new FiltrarPorVeterinario($request),
                new FiltrarPorFecha($request),
                new FiltrarPorEstado($request),
                new FiltrarPorBusqueda($request, ['appointment_date', 'appointment_time']),
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
