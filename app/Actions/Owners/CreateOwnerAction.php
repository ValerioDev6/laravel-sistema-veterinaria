<?php

namespace App\Actions\Owners;

use App\Models\Owner;

class CreateOwnerAction
{
    public static function execute(array $data = []): Owner
    {
        return Owner::create([
            "first_name" => data_get($data, "first_name"),
            "last_name" => data_get($data, "last_name"),
            "email" => data_get($data, "email"),
            "phone" => data_get($data, "phone"),
            "address" => data_get($data, "address"),
            "city" => data_get($data, "city"),
            "type_documento" => data_get($data, "type_documento"),
            "n_documento" => data_get($data, "n_documento"),
        ]);
    }
}