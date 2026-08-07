<?php

namespace App\Actions\Cirugias;

use App\Models\Surgiere;
use Illuminate\Validation\ValidationException;

class DeleteCirugiaAction
{
    public function execute(Surgiere $cirugia): void
    {
        if ($cirugia->medical_records()->count() > 0) {
            throw ValidationException::withMessages([
                "cirugia" => "No se puede eliminar la cirugía porque tiene historial médico asociado.",
            ]);
        }

        $cirugia->delete();
    }
}