<?php

namespace App\Actions\Medicines;

use App\Models\Medicine;

class CreateMedicineAction
{
    public function execute(array $data): Medicine
    {
        return Medicine::create($data);
    }
}