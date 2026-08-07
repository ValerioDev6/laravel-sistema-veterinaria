<?php

namespace App\Http\Requests\Pacientes;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "owner_id" => ["required", "integer", "exists:owners,id"],
            "species_id" => ["required", "integer", "exists:species,id"],
            "breed_id" => ["nullable", "integer", "exists:breeds,id"],
            "name" => ["required", "string", "max:100"],
            "birth_date" => ["nullable", "date", "before:today"],
            "gender" => ["required", "string", "in:macho,hembra,desconocido"],
            "color" => ["nullable", "string", "max:50"],
            "weight" => ["nullable", "numeric", "min:0", "max:1000"],
            "photo" => ["nullable", "image", "mimes:jpeg,png,jpg,webp", "max:2048"],
            "medical_notes" => ["nullable", "string"],
        ];
    }
}