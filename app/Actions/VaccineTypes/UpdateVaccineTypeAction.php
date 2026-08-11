<?php

namespace App\Actions\VaccineTypes;

use App\Models\VaccineType;

class UpdateVaccineTypeAction
{
    public static function execute(VaccineType $vaccineType, array $data = []): VaccineType
    {
        $vaccineType->update([
            "name" => data_get($data, "name"),
            "species_id" => data_get($data, "species_id"),
            "base_price" => data_get($data, "base_price"),
        ]);

        return $vaccineType->fresh();
    }
}