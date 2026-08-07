<?php

namespace App\Actions\Species;

use App\Models\Species;

class CreateSpeciesAction
{
    public function execute(array $data): Species
    {
        return Species::create($data);
    }
}