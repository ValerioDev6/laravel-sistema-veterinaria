<?php

namespace App\Actions\Citas;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ValidarDisponibilidadCita
{
    /**
     * Valida que el veterinario tenga horario activo ese día y que la
     * hora solicitada no esté ya ocupada por otra cita activa.
     * Se ejecuta dentro de la transacción del store/update.
     */
    public static function execute(
        array $data,
        ?int $ignorarCitaId = null,
    ): void {
        $date = data_get($data, 'appointment_date');
        $time = data_get($data, 'appointment_time');
        $vetId = data_get($data, 'veterinarian_id');

        if (! $date || ! $time || ! $vetId) {
            return;
        }

        $dayOfWeek = (int) \Carbon\Carbon::parse($date)->dayOfWeek;

        $tieneHorario = DB::table('veterinarian_schedules')
            ->where('veterinarian_id', $vetId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();

        if (! $tieneHorario) {
            throw ValidationException::withMessages([
                'appointment_time' =>
                    'La hora seleccionada está fuera del horario de atención del veterinario para ese día.',
            ]);
        }

        $conflicto = DB::table('citas')
            ->where('veterinarian_id', $vetId)
            ->where('appointment_date', $date)
            ->whereRaw('TIME(appointment_time) = ?', [$time])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->when($ignorarCitaId, fn ($q) => $q->where('id', '!=', $ignorarCitaId))
            ->exists();

        if ($conflicto) {
            throw ValidationException::withMessages([
                'appointment_time' =>
                    'El veterinario ya tiene una cita a esa hora.',
            ]);
        }
    }
}
