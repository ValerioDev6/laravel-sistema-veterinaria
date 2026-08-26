<?php

namespace App\Http\Requests\Veterinarios;

use App\Http\Requests\Users\UpdateUserRequest;

class UpdateVeterinarioRequest extends UpdateUserRequest
{
    public function rules(): array
    {
        return array_replace(parent::rules(), [
            "role" => ["nullable", "string", "exists:roles,name"],
        ]);
    }
}
