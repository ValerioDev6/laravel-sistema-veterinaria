<?php

namespace App\Actions\Cirugias;

use App\Models\MedicalRecord;
use App\Models\Surgiere;
use Illuminate\Support\Facades\DB;

class CreateCirugiaAction
{
    public static function execute(array $data = []): Surgiere
    {
        return DB::transaction(function () use ($data) {
            $cirugia = Surgiere::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "surgery_type" => data_get($data, "surgery_type"),
                "surgery_date" => data_get($data, "surgery_date"),
                "outcome" => data_get($data, "outcome"),
                "status" => data_get($data, "status"),
                "medical_notes" => data_get($data, "medical_notes"),
            ]);

            MedicalRecord::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "surgery_id" => $cirugia->id,
                "event_type" => "cirugia",
                "event_date" => data_get($data, "surgery_date"),
                "notes" => "Cirugía: " . (data_get($data, "surgery_type") ?? "sin tipo"),
            ]);

            return $cirugia;
        });
    }
}