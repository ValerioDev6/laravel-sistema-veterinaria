<?php

namespace App\Actions\VeterinarianSchedules;

use App\Models\VeterinarianSchedule;

class DeleteScheduleAction
{
    public function execute(VeterinarianSchedule $schedule): void
    {
        $schedule->delete();
    }
}