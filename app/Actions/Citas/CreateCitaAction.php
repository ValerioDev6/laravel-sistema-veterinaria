<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class CreateCitaAction
{
    public static function execute(array $data = []): Cita
    {
        $datos = [
            'pet_id' => data_get($data, 'pet_id'),
            'veterinarian_id' => data_get($data, 'veterinarian_id'),
            'service_id' => data_get($data, 'service_id'),
            'appointment_date' => data_get($data, 'appointment_date'),
            'appointment_time' => data_get($data, 'appointment_time'),
            'reason' => data_get($data, 'reason'),
            'reprogramming' => data_get($data, 'reprogramming'),
            'status' => data_get($data, 'status'),
            'created_by_user_id' => data_get($data, 'created_by_user_id', auth()->id()),
        ];

        return Cita::create($datos);
    }
}
