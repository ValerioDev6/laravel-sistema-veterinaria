<?php

namespace App\Actions\Cirugias;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValidarDisponibilidadCirugia
{
    /**
     * Valida que el veterinario tenga horario activo ese día y que la
     * hora solicitada no esté ocupada por una cita, vacuna u otra cirugía activa.
     * Se ejecuta dentro de la validación del store/update.
     */
    public static function execute(
        array $data,
        ?int $ignorarCirugiaId = null,
    ): void {
        $date = data_get($data, 'surgery_date');
        $time = data_get($data, 'surgery_time');
        $vetId = data_get($data, 'veterinarian_id');

        if (! $date || ! $time || ! $vetId) {
            return;
        }

        // En edición: si la fecha, hora y veterinario no cambiaron respecto a la
        // cirugía original, se permite guardar tal cual (puede ser una fecha
        // pasada o fuera del horario actual, como en los datos sembrados).
        if ($ignorarCirugiaId) {
            $actual = DB::table('surgiere')
                ->where('id', $ignorarCirugiaId)
                ->first();

            if (
                $actual
                && (int) $actual->veterinarian_id === (int) $vetId
                && $actual->surgery_date
                && \Carbon\Carbon::parse($actual->surgery_date)->format('Y-m-d') === \Carbon\Carbon::parse($date)->format('Y-m-d')
                && \Carbon\Carbon::parse($actual->surgery_date)->format('H:i') === $time
            ) {
                return;
            }
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
                'surgery_time' =>
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
                'surgery_time' =>
                    'El veterinario ya tiene una cita a esa hora.',
            ]);
        }

        $conflictoVacuna = DB::table('vacunas')
            ->where('veterinarian_id', $vetId)
            ->where('vaccination_date', $date)
            ->whereRaw('TIME(vaccination_time) = ?', [$time])
            ->exists();

        if ($conflictoVacuna) {
            throw ValidationException::withMessages([
                'surgery_time' =>
                    'El veterinario ya tiene una vacuna a esa hora.',
            ]);
        }

        $conflictoCirugia = DB::table('surgiere')
            ->where('veterinarian_id', $vetId)
            ->whereDate('surgery_date', $date)
            ->whereRaw('TIME(surgery_date) = ?', [$time])
            ->where('status', '!=', 'cancelada')
            ->when(
                $ignorarCirugiaId,
                fn ($q) => $q->where('id', '!=', $ignorarCirugiaId),
            )
            ->exists();

        if ($conflictoCirugia) {
            throw ValidationException::withMessages([
                'surgery_time' =>
                    'El veterinario ya tiene una cirugía a esa hora.',
            ]);
        }
    }
}