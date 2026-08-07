<?php

namespace App\Actions\Owners;

use App\Models\Owner;
use Illuminate\Validation\ValidationException;

class DeleteOwnerAction
{
    public function execute(Owner $owner): void
    {
        if ($owner->pacientes()->count() > 0) {
            throw ValidationException::withMessages([
                "owner" => "No se puede eliminar el propietario porque tiene mascotas registradas.",
            ]);
        }

        $owner->delete();
    }
}