<?php

namespace App\Actions\VeterinarianSchedules;

use App\Models\VeterinarianSchedule;

class CreateScheduleAction
{
    public function execute(array $data): VeterinarianSchedule
    {
        return VeterinarianSchedule::create($data);
    }
}