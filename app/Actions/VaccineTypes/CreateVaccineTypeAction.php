<?php

namespace App\Actions\VaccineTypes;

use App\Models\VaccineType;

class CreateVaccineTypeAction
{
    public static function execute(array $data = []): VaccineType
    {
        return VaccineType::create([
            "name" => data_get($data, "name"),
            "species_id" => data_get($data, "species_id"),
            "base_price" => data_get($data, "base_price"),
        ]);
    }
}