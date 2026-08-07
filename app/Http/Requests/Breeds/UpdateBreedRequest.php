<?php

namespace App\Http\Requests\Breeds;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBreedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "species_id" => ["required", "integer", "exists:species,id"],
            "name" => [
                "required",
                "string",
                "max:60",
                Rule::unique("breeds", "name")
                    ->where("species_id", $this->input("species_id"))
                    ->ignore($this->breed->id),
            ],
        ];
    }
}