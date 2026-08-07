<?php

namespace App\Actions\Cirugias;

use App\Models\Surgiere;

class UpdateCirugiaAction
{
    public static function execute(Surgiere $cirugia, array $data = []): Surgiere
    {
        $cirugia->update([
            "pet_id" => data_get($data, "pet_id"),
            "veterinarian_id" => data_get($data, "veterinarian_id"),
            "cita_id" => data_get($data, "cita_id"),
            "surgery_type" => data_get($data, "surgery_type"),
            "surgery_date" => data_get($data, "surgery_date"),
            "outcome" => data_get($data, "outcome"),
            "status" => data_get($data, "status"),
            "medical_notes" => data_get($data, "medical_notes"),
        ]);

        return $cirugia->fresh();
    }
}