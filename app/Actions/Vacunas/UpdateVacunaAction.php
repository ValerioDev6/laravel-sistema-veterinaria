<?php

namespace App\Actions\Vacunas;

use App\Models\Vacuna;

class UpdateVacunaAction
{
    public function execute(Vacuna $vacuna, array $data): Vacuna
    {
        $vacuna->update($data);

        return $vacuna->fresh()->load("vaccine_type");
    }
}