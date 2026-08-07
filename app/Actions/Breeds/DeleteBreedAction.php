<?php

namespace App\Actions\Breeds;

use App\Models\Breed;
use Illuminate\Validation\ValidationException;

class DeleteBreedAction
{
    public function execute(Breed $breed): void
    {
        if ($breed->pacientes()->count() > 0) {
            throw ValidationException::withMessages([
                "breed" => "No se puede eliminar la raza porque tiene pacientes asociados.",
            ]);
        }

        $breed->delete();
    }
}