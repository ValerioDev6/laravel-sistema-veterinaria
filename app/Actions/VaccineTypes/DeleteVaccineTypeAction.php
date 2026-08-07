<?php

namespace App\Actions\VaccineTypes;

use App\Models\VaccineType;
use Illuminate\Validation\ValidationException;

class DeleteVaccineTypeAction
{
    public function execute(VaccineType $vaccineType): void
    {
        if ($vaccineType->vacunas()->count() > 0) {
            throw ValidationException::withMessages([
                "vaccine_type" => "No se puede eliminar el tipo de vacuna porque tiene vacunas registradas.",
            ]);
        }

        $vaccineType->delete();
    }
}