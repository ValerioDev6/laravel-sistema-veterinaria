<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
                "unique:services,name,{$this->service->id}",
            ],
            "description" => ["nullable", "string"],
            "category" => ["required", "in:consulta,vacunacion,cirugia,estetica,otro"],
            "base_price" => ["required", "numeric", "min:0"],
            "duration_minutes" => ["required", "integer", "min:1", "max:600"],
        ];
    }
}