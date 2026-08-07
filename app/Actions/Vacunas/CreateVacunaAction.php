<?php

namespace App\Actions\Vacunas;

use App\Models\MedicalRecord;
use App\Models\Reminder;
use App\Models\Vacuna;
use Illuminate\Support\Facades\DB;

class CreateVacunaAction
{
    public static function execute(array $data = []): Vacuna
    {
        return DB::transaction(function () use ($data) {
            $vacuna = Vacuna::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "vaccine_type_id" => data_get($data, "vaccine_type_id"),
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_date" => data_get($data, "vaccination_date"),
                "next_due_date" => data_get($data, "next_due_date"),
            ]);

            MedicalRecord::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_id" => $vacuna->id,
                "event_type" => "vacuna",
                "event_date" => data_get($data, "vaccination_date"),
                "notes" => "Vacunación: " . ($vacuna->vaccine_type?->name ?? ""),
            ]);

            if (!empty(data_get($data, "next_due_date"))) {
                Reminder::create([
                    "pet_id" => data_get($data, "pet_id"),
                    "remindable_type" => "vacuna",
                    "remindable_id" => $vacuna->id,
                    "remind_at" => data_get($data, "next_due_date"),
                    "message" => "Próxima dosis de " . ($vacuna->vaccine_type?->name ?? "vacuna"),
                    "status" => "pendiente",
                ]);
            }

            return $vacuna->load("vaccine_type");
        });
    }
}