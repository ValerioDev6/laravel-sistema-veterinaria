<?php

namespace App\Actions\MedicalRecords;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\VitalSign;
use Illuminate\Support\Facades\DB;

class CreateMedicalRecordAction
{
    public function execute(array $data): MedicalRecord
    {
        return DB::transaction(function () use ($data) {
            $prescriptions = $data["prescriptions"] ?? [];
            $vitalSigns = $data["vital_signs"] ?? [];
            unset($data["prescriptions"], $data["vital_signs"]);

            $record = MedicalRecord::create($data);

            foreach ($prescriptions as $prescription) {
                Prescription::create([
                    "medical_record_id" => $record->id,
                    "medicine_id" => $prescription["medicine_id"],
                    "dosage" => $prescription["dosage"],
                    "duration_days" => $prescription["duration_days"] ?? null,
                ]);
            }

            if (!empty($vitalSigns) && array_filter($vitalSigns)) {
                VitalSign::create([
                    "pet_id" => $data["pet_id"],
                    "medical_record_id" => $record->id,
                    "weight" => $vitalSigns["weight"] ?? null,
                    "temperature" => $vitalSigns["temperature"] ?? null,
                    "heart_rate" => $vitalSigns["heart_rate"] ?? null,
                    "recorded_at" => now(),
                ]);
            }

            return $record->load(["prescriptions.medicine", "vital_signs"]);
        });
    }
}