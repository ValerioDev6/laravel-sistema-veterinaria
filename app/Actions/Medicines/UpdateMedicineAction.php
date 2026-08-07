<?php

namespace App\Actions\Medicines;

use App\Models\Medicine;

class UpdateMedicineAction
{
    public function execute(Medicine $medicine, array $data): Medicine
    {
        $medicine->update($data);

        return $medicine->fresh();
    }
}