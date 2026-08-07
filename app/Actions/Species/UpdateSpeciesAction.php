<?php

namespace App\Actions\Species;

use App\Models\Species;

class UpdateSpeciesAction
{
    public static function execute(Species $species, array $data = []): Species
    {
        $species->update([
            "name" => data_get($data, "name"),
        ]);

        return $species->fresh();
    }
}