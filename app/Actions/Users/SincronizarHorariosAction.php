<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Models\VeterinarianSchedule;

class SincronizarHorariosAction
{
    public static function execute(User $user, ?array $horarios): void
    {
        $user->veterinarian_schedules()->delete();

        foreach ($horarios ?? [] as $horario) {
            VeterinarianSchedule::create([
                "veterinarian_id" => $user->id,
                "day_of_week" => (int) $horario["day_of_week"],
                "start_time" => $horario["start_time"],
                "end_time" => $horario["end_time"],
                "is_active" => true,
            ]);
        }
    }
}
