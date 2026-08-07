<?php

namespace App\Http\Requests\Owners;

use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:100"],
            "last_name" => ["required", "string", "max:100"],
            "email" => ["nullable", "email", "max:100"],
            "phone" => ["required", "string", "max:20"],
            "address" => ["nullable", "string", "max:200"],
            "city" => ["nullable", "string", "max:100"],
            "type_documento" => ["nullable", "string", "max:50", "in:DNI,CE,Pasaporte"],
            "n_documento" => ["nullable", "string", "max:50"],
        ];
    }
}