<?php

namespace App\Actions\VeterinarianSchedules;

use App\Models\VeterinarianSchedule;

class UpdateScheduleAction
{
    public function execute(VeterinarianSchedule $schedule, array $data): VeterinarianSchedule
    {
        $schedule->update($data);

        return $schedule->fresh();
    }
}