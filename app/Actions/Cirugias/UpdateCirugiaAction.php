<?php

namespace App\Actions\Cirugias;

use App\Models\Surgiere;

class UpdateCirugiaAction
{
    public function execute(Surgiere $cirugia, array $data): Surgiere
    {
        $cirugia->update($data);

        return $cirugia->fresh();
    }
}