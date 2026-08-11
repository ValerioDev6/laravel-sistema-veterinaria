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

        $perfiles = [
            // dr.torres: Lun a Vie, horario partido con cierre 19:00
            [
                "dias" => [1, 2, 3, 4, 5],
                "rangos" => [["09:00", "13:00"], ["15:00", "19:00"]],
            ],
            // dr.ramos: Lun a Sáb, horario corrido amplio hasta 18:30
            [
                "dias" => [1, 2, 3, 4, 5, 6],
                "rangos" => [["08:00", "12:30"], ["14:00", "18:30"]],
            ],
            // dr.paredes: Lun a Vie, horario extendido hasta 21:00
            [
                "dias" => [1, 2, 3, 4, 5],
                "rangos" => [["07:00", "12:00"], ["16:00", "21:00"]],
            ],
        ];

        foreach ($veterinarians as $index => $vet) {
            $perfil = $perfiles[$index % count($perfiles)];

            foreach ($perfil["dias"] as $day) {
                foreach ($perfil["rangos"] as [$inicio, $fin]) {
                    VeterinarianSchedule::updateOrCreate(
                        [
                            "veterinarian_id" => $vet->id,
                            "day_of_week" => $day,
                            "start_time" => $inicio,
                        ],
                        [
                            "end_time" => $fin,
                            "is_active" => true,
                        ],
                    );
                }
            }
        }
    }
}