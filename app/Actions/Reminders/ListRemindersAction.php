<?php

namespace App\Actions\Reminders;

use App\Filters\Reminders\FiltrarPorBusquedaReminders;
use App\Filters\Reminders\FiltrarPorEstadoReminder;
use App\Filters\Reminders\FiltrarPorPetReminder;
use App\Filters\Reminders\FiltrarPorTipoReminder;
use App\Filters\Shared\OrdenarPor;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListRemindersAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Reminder::with(["paciente"]))
            ->through([
                new FiltrarPorBusquedaReminders($request),
                new FiltrarPorTipoReminder($request),
                new FiltrarPorPetReminder($request),
                new FiltrarPorEstadoReminder($request),
                new OrdenarPor($request, [0 => "id", 1 => "remind_at"], [0, "desc"]),
            ])
            ->thenReturn();

        return $query->paginate($request->integer("per_page", 15));
    }
}