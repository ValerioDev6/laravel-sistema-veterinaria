<?php

namespace App\Actions\Breeds;

use App\Models\Breed;

class UpdateBreedAction
{
    public function execute(Breed $breed, array $data): Breed
    {
        $breed->update($data);

        return $breed->fresh();
    }
}