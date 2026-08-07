<?php

namespace App\Actions\Breeds;

use App\Models\Breed;

class CreateBreedAction
{
    public static function execute(array $data = []): Breed
    {
        return Breed::create([
            "species_id" => data_get($data, "species_id"),
            "name" => data_get($data, "name"),
        ]);
    }
}