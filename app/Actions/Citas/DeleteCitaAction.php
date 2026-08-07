<?php

namespace App\Actions\Citas;

use App\Models\Cita;
use Illuminate\Validation\ValidationException;

class DeleteCitaAction
{
    public function execute(Cita $cita): void
    {
        if ($cita->medical_records()->count() > 0) {
            throw ValidationException::withMessages([
                "cita" => "No se puede eliminar la cita porque tiene historial médico asociado.",
            ]);
        }

        $cita->delete();
    }
}