<?php

namespace App\Actions\VaccineTypes;

use App\Models\VaccineType;

class UpdateVaccineTypeAction
{
    public function execute(VaccineType $vaccineType, array $data): VaccineType
    {
        $vaccineType->update($data);

        return $vaccineType->fresh();
    }
}