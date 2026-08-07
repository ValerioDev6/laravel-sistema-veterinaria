<?php

namespace App\Http\Requests\Medicines;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
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
                "unique:medicines,name",
            ],
            "quantity" => ["required", "integer", "min:0"],
            "unit_cost" => ["required", "numeric", "min:0", "max:9999999.99"],
        ];
    }
}
