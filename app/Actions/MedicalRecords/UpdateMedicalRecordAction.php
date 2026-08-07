<?php

namespace App\Actions\MedicalRecords;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\VitalSign;
use Illuminate\Support\Facades\DB;

class UpdateMedicalRecordAction
{
    public function execute(MedicalRecord $record, array $data): MedicalRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $prescriptions = $data["prescriptions"] ?? [];
            $vitalSigns = $data["vital_signs"] ?? [];
            unset($data["prescriptions"], $data["vital_signs"]);

            $record->update($data);

            $record->prescriptions()->delete();
            foreach ($prescriptions as $prescription) {
                Prescription::create([
                    "medical_record_id" => $record->id,
                    "medicine_id" => $prescription["medicine_id"],
                    "dosage" => $prescription["dosage"],
                    "duration_days" => $prescription["duration_days"] ?? null,
                ]);
            }

            if (!empty($vitalSigns) && array_filter($vitalSigns)) {
                VitalSign::updateOrCreate(
                    ["medical_record_id" => $record->id],
                    [
                        "pet_id" => $data["pet_id"],
                        "weight" => $vitalSigns["weight"] ?? null,
                        "temperature" => $vitalSigns["temperature"] ?? null,
                        "heart_rate" => $vitalSigns["heart_rate"] ?? null,
                        "recorded_at" => now(),
                    ],
                );
            }

            return $record->fresh(["prescriptions.medicine", "vital_signs"]);
        });
    }
}