<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class CreateCitaAction
{
    public function execute(array $data): Cita
    {
        $data["created_by_user_id"] =
            $data["created_by_user_id"] ?? auth()->id();

        return Cita::create($data);
    }
}
