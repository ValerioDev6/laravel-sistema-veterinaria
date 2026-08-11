<?php

namespace App\Actions\Vacunas;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidarDisponibilidadVacuna
{
    /**
     * Valida que el veterinario tenga horario activo ese día y que la
     * hora solicitada no esté ya ocupada por una cita u otra vacuna activa.
     * Se ejecuta dentro de la transacción del store/update.
     */
    public static function execute(
        array $data,
        ?int $ignorarVacunaId = null,
    ): void {
        $date = data_get($data, 'vaccination_date');
        $time = data_get($data, 'vaccination_time');
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
                'vaccination_time' =>
                    'La hora seleccionada está fuera del horario de atención del veterinario para ese día.',
            ]);
        }

        $conflictoCita = DB::table('citas')
            ->where('veterinarian_id', $vetId)
            ->where('appointment_date', $date)
            ->whereRaw('TIME(appointment_time) = ?', [$time])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();

        if ($conflictoCita) {
            throw ValidationException::withMessages([
                'vaccination_time' =>
                    'El veterinario ya tiene una cita a esa hora.',
            ]);
        }

        $conflictoVacuna = DB::table('vacunas')
            ->where('veterinarian_id', $vetId)
            ->where('vaccination_date', $date)
            ->whereRaw('TIME(vaccination_time) = ?', [$time])
            ->when(
                $ignorarVacunaId,
                fn ($q) => $q->where('id', '!=', $ignorarVacunaId),
            )
            ->exists();

        if ($conflictoVacuna) {
            throw ValidationException::withMessages([
                'vaccination_time' =>
                    'El veterinario ya tiene una vacuna a esa hora.',
            ]);
        }
    }
}