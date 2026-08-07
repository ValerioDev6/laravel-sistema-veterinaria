<?php

namespace App\Actions\Medicines;

use App\Models\Medicine;

class CreateMedicineAction
{
    public static function execute(array $data = []): Medicine
    {
        return Medicine::create([
            "name" => data_get($data, "name"),
            "quantity" => data_get($data, "quantity"),
            "unit_cost" => data_get($data, "unit_cost"),
        ]);
    }
}