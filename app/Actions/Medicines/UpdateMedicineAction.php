<?php

namespace App\Actions\Medicines;

use App\Models\Medicine;

class UpdateMedicineAction
{
    public static function execute(Medicine $medicine, array $data = []): Medicine
    {
        $medicine->update([
            "name" => data_get($data, "name"),
            "quantity" => data_get($data, "quantity"),
            "unit_cost" => data_get($data, "unit_cost"),
        ]);

        return $medicine->fresh();
    }
}