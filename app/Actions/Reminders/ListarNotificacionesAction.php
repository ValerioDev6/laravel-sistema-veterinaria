<?php

namespace App\Actions\Reminders;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Collection;

class ListarNotificacionesAction
{
    /**
     * Recordatorios que requieren atención: pendientes cuyo día de
     * recordatorio llegó (hoy) o ya pasó (vencidos), más próximos hoy.
     */
    public function execute(): array
    {
        $items = Reminder::with(["paciente.owner"])
            ->where("status", "pendiente")
            ->whereDate("remind_at", "<=", now()->toDateString())
            ->orderBy("remind_at", "desc")
            ->get();

        return [
            "count" => $items->count(),
            "items" => $items,
        ];
    }
}