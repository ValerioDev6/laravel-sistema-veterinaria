<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class UpdateCitaAction
{
    public function execute(Cita $cita, array $data): Cita
    {
        $cita->update($data);

        return $cita->fresh();
    }
}