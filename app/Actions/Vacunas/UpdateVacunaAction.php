<?php

namespace App\Actions\Vacunas;

use App\Models\Vacuna;

class UpdateVacunaAction
{
    public static function execute(Vacuna $vacuna, array $data = []): Vacuna
    {
        $vacuna->update([
            "pet_id" => data_get($data, "pet_id"),
            "veterinarian_id" => data_get($data, "veterinarian_id"),
            "vaccine_type_id" => data_get($data, "vaccine_type_id"),
            "cita_id" => data_get($data, "cita_id"),
            "vaccination_date" => data_get($data, "vaccination_date"),
            "next_due_date" => data_get($data, "next_due_date"),
        ]);

        return $vacuna->fresh()->load("vaccine_type");
    }
}