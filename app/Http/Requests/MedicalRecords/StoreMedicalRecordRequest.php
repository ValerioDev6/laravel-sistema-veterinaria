<?php

namespace App\Http\Requests\MedicalRecords;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "pet_id" => ["required", "integer", "exists:pacientes,id"],
            "veterinarian_id" => ["required", "integer", "exists:users,id"],
            "cita_id" => ["nullable", "integer", "exists:citas,id"],
            "event_type" => ["required", "string", "in:consulta,vacuna,cirugia,otro"],
            "event_date" => ["required", "date"],
            "notes" => ["nullable", "string"],
            "prescriptions" => ["nullable", "array"],
            "prescriptions.*.medicine_id" => ["required_with:prescriptions", "integer", "exists:medicines,id"],
            "prescriptions.*.dosage" => ["required_with:prescriptions", "string", "max:60"],
            "prescriptions.*.duration_days" => ["nullable", "integer", "min:1"],
            "vital_signs" => ["nullable", "array"],
            "vital_signs.weight" => ["nullable", "numeric", "min:0", "max:1000"],
            "vital_signs.temperature" => ["nullable", "numeric", "min:30", "max:45"],
            "vital_signs.heart_rate" => ["nullable", "integer", "min:0", "max:500"],
        ];
    }
}