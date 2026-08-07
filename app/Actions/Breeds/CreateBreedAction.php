<?php

namespace App\Actions\Breeds;

use App\Models\Breed;

class CreateBreedAction
{
    public function execute(array $data): Breed
    {
        return Breed::create($data);
    }
}