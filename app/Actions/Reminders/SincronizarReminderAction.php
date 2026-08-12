<?php

namespace App\Actions\Reminders;

use App\Models\Reminder;
use Carbon\Carbon;

class SincronizarReminderAction
{
    /**
     * Regla automática por tipo:
     *  - cita    → 1 día antes de la cita
     *  - cirugia → 2 días antes de la cirugía
     *  - vacuna  → el mismo día de la próxima dosis
     *
     * Si no hay fecha de evento, elimina el recordatorio asociado.
     */
    public static function execute(
        string $tipo,
        int $petId,
        int $remindableId,
        string|Carbon|null $eventDate,
        string $message,
    ): void {
        $daysBefore = match ($tipo) {
            "cita" => 1,
            "cirugia", "surgiere" => 2,
            default => 0,
        };

        $event = $eventDate ? Carbon::parse($eventDate) : null;
        $remindAt = $event ? $event->copy()->subDays($daysBefore) : null;

        $query = Reminder::where("pet_id", $petId)
            ->where("remindable_type", $tipo)
            ->where("remindable_id", $remindableId);

        if (! $remindAt) {
            $query->delete();

            return;
        }

        $existing = $query->first();

        if (
            $existing
            && $existing->remind_at
            && $existing->remind_at->format("Y-m-d") === $remindAt->format("Y-m-d")
        ) {
            // Misma fecha: se conserva el estado (enviado/cancelado/pendiente).
            return;
        }

        Reminder::updateOrCreate(
            [
                "pet_id" => $petId,
                "remindable_type" => $tipo,
                "remindable_id" => $remindableId,
            ],
            [
                "remind_at" => $remindAt->toDateString(),
                "message" => $message,
                "status" => "pendiente",
            ],
        );
    }
}