<?php

namespace App\Actions\VaccineTypes;

use App\Models\VaccineType;

class CreateVaccineTypeAction
{
    public function execute(array $data): VaccineType
    {
        return VaccineType::create($data);
    }
}