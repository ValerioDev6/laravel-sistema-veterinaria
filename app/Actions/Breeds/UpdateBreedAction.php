<?php

namespace App\Actions\Breeds;

use App\Models\Breed;

class UpdateBreedAction
{
    public static function execute(Breed $breed, array $data = []): Breed
    {
        $breed->update([
            "species_id" => data_get($data, "species_id"),
            "name" => data_get($data, "name"),
        ]);

        return $breed->fresh();
    }
}