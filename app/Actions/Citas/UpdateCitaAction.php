<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class UpdateCitaAction
{
    public static function execute(Cita $cita, array $data = []): Cita
    {
        $cita->update([
            "pet_id" => data_get($data, "pet_id"),
            "veterinarian_id" => data_get($data, "veterinarian_id"),
            "service_id" => data_get($data, "service_id"),
            "appointment_date" => data_get($data, "appointment_date"),
            "appointment_time" => data_get($data, "appointment_time"),
            "reason" => data_get($data, "reason"),
            "reprogramming" => data_get($data, "reprogramming"),
            "status" => data_get($data, "status"),
        ]);

        return $cita->fresh();
    }
}