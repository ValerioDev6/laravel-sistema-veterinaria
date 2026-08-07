<?php

namespace App\Http\Requests\Branches;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:100"],
            "address" => ["required", "string", "max:200"],
            "city" => ["required", "string", "max:100"],
            "phone" => ["nullable", "string", "max:20"],
        ];
    }
}