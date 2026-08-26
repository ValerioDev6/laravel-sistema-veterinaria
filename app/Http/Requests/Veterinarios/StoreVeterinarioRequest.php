<?php

namespace App\Http\Requests\Veterinarios;

use App\Http\Requests\Users\StoreUserRequest;

class StoreVeterinarioRequest extends StoreUserRequest
{
    public function rules(): array
    {
        return array_replace(parent::rules(), [
            "role" => ["nullable", "string", "exists:roles,name"],
        ]);
    }
}
