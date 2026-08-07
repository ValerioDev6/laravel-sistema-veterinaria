<?php

namespace App\Actions\Vacunas;

use App\Models\Vacuna;
use Illuminate\Validation\ValidationException;

class DeleteVacunaAction
{
    public function execute(Vacuna $vacuna): void
    {
        if ($vacuna->medical_records()->count() > 0) {
            throw ValidationException::withMessages([
                "vacuna" => "No se puede eliminar la vacuna porque tiene historial médico asociado.",
            ]);
        }

        $vacuna->delete();
    }
}