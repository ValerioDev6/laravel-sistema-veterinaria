<?php

namespace App\Http\Requests\VaccineTypes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVaccineTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                "max:100",
                "unique:vaccine_types,name,{$this->vaccineType->id}",
            ],
            "species_id" => ["nullable", "integer", "exists:species,id"],
        ];
    }
}