<?php

namespace App\Actions\Cirugias;

use App\Models\MedicalRecord;
use App\Models\Surgiere;
use Illuminate\Support\Facades\DB;

class CreateCirugiaAction
{
    public function execute(array $data): Surgiere
    {
        return DB::transaction(function () use ($data) {
            $cirugia = Surgiere::create($data);

            MedicalRecord::create([
                "pet_id" => $data["pet_id"],
                "veterinarian_id" => $data["veterinarian_id"],
                "cita_id" => $data["cita_id"] ?? null,
                "surgery_id" => $cirugia->id,
                "event_type" => "cirugia",
                "event_date" => $data["surgery_date"],
                "notes" => "Cirugía: " . ($data["surgery_type"] ?? "sin tipo"),
            ]);

            return $cirugia;
        });
    }
}