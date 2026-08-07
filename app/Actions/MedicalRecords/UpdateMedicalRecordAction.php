<?php

namespace App\Actions\MedicalRecords;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\VitalSign;
use Illuminate\Support\Facades\DB;

class UpdateMedicalRecordAction
{
    public static function execute(MedicalRecord $record, array $data = []): MedicalRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $prescriptions = data_get($data, "prescriptions", []);
            $vitalSigns = data_get($data, "vital_signs", []);

            $record->update([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_id" => data_get($data, "vaccination_id"),
                "surgery_id" => data_get($data, "surgery_id"),
                "event_type" => data_get($data, "event_type"),
                "event_date" => data_get($data, "event_date"),
                "notes" => data_get($data, "notes"),
            ]);

            $record->prescriptions()->delete();
            foreach ($prescriptions as $prescription) {
                Prescription::create([
                    "medical_record_id" => $record->id,
                    "medicine_id" => data_get($prescription, "medicine_id"),
                    "dosage" => data_get($prescription, "dosage"),
                    "duration_days" => data_get($prescription, "duration_days"),
                ]);
            }

            if (!empty($vitalSigns) && array_filter($vitalSigns)) {
                VitalSign::updateOrCreate(
                    ["medical_record_id" => $record->id],
                    [
                        "pet_id" => data_get($data, "pet_id"),
                        "weight" => data_get($vitalSigns, "weight"),
                        "temperature" => data_get($vitalSigns, "temperature"),
                        "heart_rate" => data_get($vitalSigns, "heart_rate"),
                        "recorded_at" => now(),
                    ],
                );
            }

            return $record->fresh(["prescriptions.medicine", "vital_signs"]);
        });
    }
}