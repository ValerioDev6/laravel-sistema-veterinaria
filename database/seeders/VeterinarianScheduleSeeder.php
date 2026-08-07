<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VeterinarianSchedule;
use Illuminate\Database\Seeder;

class VeterinarianScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $veterinarians = User::role("Veterinario")->get();

        $weekDays = [1, 2, 3, 4, 5];

        foreach ($veterinarians as $vet) {
            foreach ($weekDays as $day) {
                VeterinarianSchedule::updateOrCreate(
                    [
                        "veterinarian_id" => $vet->id,
                        "day_of_week" => $day,
                        "start_time" => "09:00",
                    ],
                    [
                        "end_time" => "13:00",
                        "is_active" => true,
                    ],
                );
                VeterinarianSchedule::updateOrCreate(
                    [
                        "veterinarian_id" => $vet->id,
                        "day_of_week" => $day,
                        "start_time" => "15:00",
                    ],
                    [
                        "end_time" => "19:00",
                        "is_active" => true,
                    ],
                );
            }
        }
    }
}