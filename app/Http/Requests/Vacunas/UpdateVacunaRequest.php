<?php

namespace App\Http\Requests\Vacunas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVacunaRequest extends FormRequest
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
            "vaccine_type_id" => ["required", "integer", "exists:vaccine_types,id"],
            "cita_id" => ["nullable", "integer", "exists:citas,id"],
            "vaccination_date" => ["required", "date"],
            "next_due_date" => ["nullable", "date", "after_or_equal:vaccination_date"],
        ];
    }
}