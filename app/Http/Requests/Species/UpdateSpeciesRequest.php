<?php

namespace App\Http\Requests\Species;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpeciesRequest extends FormRequest
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
                "max:50",
                "unique:species,name,{$this->species->id}",
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "name.unique" => "El nombre ya existe.",
            "name.required" => "El nombre es obligatorio.",
        ];
    }
}