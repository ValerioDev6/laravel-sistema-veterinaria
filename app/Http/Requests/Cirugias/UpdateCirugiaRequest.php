<?php

namespace App\Http\Requests\Cirugias;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCirugiaRequest extends FormRequest
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
            "surgery_type" => ["nullable", "string", "max:100"],
            "surgery_date" => ["required", "date"],
            "outcome" => ["nullable", "string"],
            "status" => ["required", "string", "in:pendiente,en_proceso,completada,cancelada"],
            "medical_notes" => ["nullable", "string"],
        ];
    }
}