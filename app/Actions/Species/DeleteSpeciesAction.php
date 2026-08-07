<?php

namespace App\Actions\Species;

use App\Models\Species;
use Illuminate\Validation\ValidationException;

class DeleteSpeciesAction
{
    public function execute(Species $species): void
    {
        $dependencias = collect([
            "razas" => $species->breeds()->count(),
            "pacientes" => $species->pacientes()->count(),
            "tipos de vacuna" => $species->vaccine_types()->count(),
        ])->filter(fn ($count) => $count > 0);

        if ($dependencias->isNotEmpty()) {
            throw ValidationException::withMessages([
                "species" => "No se puede eliminar la especie porque tiene registros asociados: ".
                    $dependencias->keys()->implode(", ").".",
            ]);
        }

        $species->delete();
    }
}