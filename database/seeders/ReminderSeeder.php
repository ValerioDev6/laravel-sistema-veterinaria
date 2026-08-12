<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Reminder;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run(): void
    {
        $this->remindersDeVacunas();
        $this->remindersDeCitas();
        $this->remindersDeCirugias();
    }

    private function remindersDeVacunas(): void
    {
        Vacuna::query()
            ->with(["vaccine_type"])
            ->whereNotNull("next_due_date")
            ->orderBy("vaccination_date")
            ->get()
            ->each(function (Vacuna $vacuna, int $index) {
                Reminder::updateOrCreate(
                    [
                        "pet_id" => $vacuna->pet_id,
                        "remindable_type" => "vacuna",
                        "remindable_id" => $vacuna->id,
                    ],
                    [
                        "remind_at" => $vacuna->next_due_date,
                        "message" => "Próxima dosis de " . ($vacuna->vaccine_type?->name ?? "vacuna"),
                        "status" => $index % 2 === 0 ? "pendiente" : "enviado",
                    ],
                );
            });
    }

    private function remindersDeCitas(): void
    {
        Cita::query()
            ->whereIn("status", ["confirmada", "pendiente"])
            ->orderBy("appointment_date")
            ->take(6)
            ->get()
            ->each(function (Cita $cita) {
                Reminder::updateOrCreate(
                    [
                        "pet_id" => $cita->pet_id,
                        "remindable_type" => "cita",
                        "remindable_id" => $cita->id,
                    ],
                    [
                        "remind_at" => $cita->appointment_date?->copy()->setTimeFromTimeString($cita->appointment_time?->format("H:i")),
                        "message" => "Cita de " . ($cita->service?->name ?? "consulta") . ": " . ($cita->reason ?? ""),
                        "status" => "pendiente",
                    ],
                );
            });
    }

    private function remindersDeCirugias(): void
    {
        Surgiere::query()
            ->where("status", "en_proceso")
            ->orderBy("surgery_date")
            ->get()
            ->each(function (Surgiere $surgiere) {
                Reminder::updateOrCreate(
                    [
                        "pet_id" => $surgiere->pet_id,
                        "remindable_type" => "cirugia",
                        "remindable_id" => $surgiere->id,
                    ],
                    [
                        "remind_at" => $surgiere->surgery_date,
                        "message" => "Cirugía de " . ($surgiere->surgery_type ?? "cirugía") . " programada",
                        "status" => "pendiente",
                    ],
                );
            });
    }
}