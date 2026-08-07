<?php

namespace App\Actions\VeterinarianSchedules;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\VeterinarianSchedule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListVeterinarianSchedulesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(VeterinarianSchedule::with('user'))
            ->through([
                new FiltrarPorBusqueda($request, ['day_of_week']),
                new OrdenarPor($request, [0 => 'id', 1 => 'veterinarian_id', 2 => 'day_of_week', 3 => 'start_time'], [2, 'asc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}