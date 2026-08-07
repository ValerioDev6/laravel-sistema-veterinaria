<?php

namespace App\Actions\Species;

use App\Models\Species;

class CreateSpeciesAction
{
    public static function execute(array $data = []): Species
    {
        return Species::create([
            "name" => data_get($data, "name"),
        ]);
    }
}