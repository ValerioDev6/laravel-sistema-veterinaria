<?php

namespace App\Actions\VeterinarianSchedules;

use App\Models\VeterinarianSchedule;

class UpdateScheduleAction
{
    public static function execute(VeterinarianSchedule $schedule, array $data = []): VeterinarianSchedule
    {
        $schedule->update([
            "veterinarian_id" => data_get($data, "veterinarian_id"),
            "day_of_week" => data_get($data, "day_of_week"),
            "start_time" => data_get($data, "start_time"),
            "end_time" => data_get($data, "end_time"),
            "is_active" => data_get($data, "is_active"),
        ]);

        return $schedule->fresh();
    }
}