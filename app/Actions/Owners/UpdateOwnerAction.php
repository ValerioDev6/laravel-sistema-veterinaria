<?php

namespace App\Actions\Owners;

use App\Models\Owner;

class UpdateOwnerAction
{
    public static function execute(Owner $owner, array $data = []): Owner
    {
        $owner->update([
            "first_name" => data_get($data, "first_name"),
            "last_name" => data_get($data, "last_name"),
            "email" => data_get($data, "email"),
            "phone" => data_get($data, "phone"),
            "address" => data_get($data, "address"),
            "city" => data_get($data, "city"),
            "type_documento" => data_get($data, "type_documento"),
            "n_documento" => data_get($data, "n_documento"),
        ]);

        return $owner->fresh();
    }
}