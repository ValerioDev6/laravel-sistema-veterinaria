<?php

namespace App\Actions\Species;

use App\Models\Species;

class UpdateSpeciesAction
{
    public function execute(Species $species, array $data): Species
    {
        $species->update($data);

        return $species->fresh();
    }
}