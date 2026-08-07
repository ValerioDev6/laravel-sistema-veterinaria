<?php

namespace App\Actions\Cirugias;

use App\Models\Surgiere;

class CambiarEstadoCirugiaAction
{
    public function execute(Surgiere $cirugia, string $status): Surgiere
    {
        $cirugia->update(["status" => $status]);

        return $cirugia->fresh();
    }
}