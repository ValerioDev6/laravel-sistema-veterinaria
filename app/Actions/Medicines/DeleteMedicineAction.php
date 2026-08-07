<?php

namespace App\Actions\Medicines;

use App\Models\Medicine;
use Illuminate\Validation\ValidationException;

class DeleteMedicineAction
{
    public function execute(Medicine $medicine): void
    {
        if ($medicine->prescriptions()->count() > 0) {
            throw ValidationException::withMessages([
                "medicine" => "No se puede eliminar el medicamento porque tiene prescripciones registradas.",
            ]);
        }

        $medicine->delete();
    }
}