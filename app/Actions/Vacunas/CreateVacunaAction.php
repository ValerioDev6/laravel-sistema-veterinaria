<?php

namespace App\Actions\Vacunas;

use App\Models\MedicalRecord;
use App\Models\Reminder;
use App\Models\Vacuna;
use Illuminate\Support\Facades\DB;

class CreateVacunaAction
{
    public function execute(array $data): Vacuna
    {
        return DB::transaction(function () use ($data) {
            $vacuna = Vacuna::create($data);

            MedicalRecord::create([
                "pet_id" => $data["pet_id"],
                "veterinarian_id" => $data["veterinarian_id"],
                "cita_id" => $data["cita_id"] ?? null,
                "vaccination_id" => $vacuna->id,
                "event_type" => "vacuna",
                "event_date" => $data["vaccination_date"],
                "notes" => "Vacunación: " . ($vacuna->vaccine_type?->name ?? ""),
            ]);

            if (!empty($data["next_due_date"])) {
                Reminder::create([
                    "pet_id" => $data["pet_id"],
                    "remindable_type" => "vacuna",
                    "remindable_id" => $vacuna->id,
                    "remind_at" => $data["next_due_date"],
                    "message" => "Próxima dosis de " . ($vacuna->vaccine_type?->name ?? "vacuna"),
                    "status" => "pendiente",
                ]);
            }

            return $vacuna->load("vaccine_type");
        });
    }
}