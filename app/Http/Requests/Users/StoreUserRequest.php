<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "username" => ["required", "string", "max:255", "unique:users,username"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "password" => ["required", "string", "min:8", "confirmed"],
            "phone" => ["nullable", "string", "max:20"],
            "type_documento" => ["nullable", "string", "in:DNI,CE,Pasaporte,RUC"],
            "n_documento" => ["nullable", "string", "max:50"],
            "birthday" => ["nullable", "date"],
            "branch_id" => ["nullable", "integer", "exists:branches,id"],
            "role" => ["required", "string", "exists:roles,name"],
            "avatar" => ["nullable", "image", "max:2048"],
        ];
    }
}