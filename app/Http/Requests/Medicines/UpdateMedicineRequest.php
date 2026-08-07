<?php

namespace App\Http\Requests\Medicines;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineRequest extends FormRequest
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
                "unique:medicines,name,{$this->medicine->id}",
            ],
            "quantity" => ["required", "integer", "min:0"],
            "unit_cost" => ["required", "numeric", "min:0", "max:9999999.99"],
        ];
    }
}