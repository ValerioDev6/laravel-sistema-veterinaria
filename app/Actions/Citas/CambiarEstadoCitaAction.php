<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class CambiarEstadoCitaAction
{
    public function execute(Cita $cita, string $status): Cita
    {
        $cita->update(["status" => $status]);

        return $cita->fresh();
    }
}