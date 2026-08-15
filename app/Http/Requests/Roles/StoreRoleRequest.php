<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
                Rule::unique("roles", "name")->where("guard_name", "api"),
            ],
            "permissions" => ["nullable", "array"],
            "permissions.*" => ["integer", "exists:permissions,id"],
        ];
    }
}
