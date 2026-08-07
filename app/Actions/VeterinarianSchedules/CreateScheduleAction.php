<?php

namespace App\Actions\VeterinarianSchedules;

use App\Models\VeterinarianSchedule;

class CreateScheduleAction
{
    public static function execute(array $data = []): VeterinarianSchedule
    {
        return VeterinarianSchedule::create([
            "veterinarian_id" => data_get($data, "veterinarian_id"),
            "day_of_week" => data_get($data, "day_of_week"),
            "start_time" => data_get($data, "start_time"),
            "end_time" => data_get($data, "end_time"),
            "is_active" => data_get($data, "is_active"),
        ]);
    }
}