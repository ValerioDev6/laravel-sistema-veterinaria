<?php

namespace App\Http\Requests\Citas;

use App\Models\Cita;
use App\Models\VeterinarianSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "pet_id" => ["required", "integer", "exists:pacientes,id"],
            "veterinarian_id" => ["required", "integer", "exists:users,id"],
            "service_id" => ["nullable", "integer", "exists:services,id"],
            "appointment_date" => ["required", "date", "after_or_equal:today"],
            "appointment_time" => ["required", "date_format:H:i"],
            "reason" => ["nullable", "string"],
            "reprogramming" => ["boolean"],
            "status" => ["required", "string", "in:pendiente,confirmada,completada,cancelada"],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $this->validarHorario($validator);
            },
        ];
    }

    protected function validarHorario(Validator $validator): void
    {
        $date = $this->input("appointment_date");
        $time = $this->input("appointment_time");
        $vet = $this->input("veterinarian_id");

        if (! $date || ! $time || ! $vet) {
            return;
        }

        $dayOfWeek = (int) \Carbon\Carbon::parse($date)->dayOfWeek;

        $schedule = VeterinarianSchedule::where("veterinarian_id", $vet)
            ->where("day_of_week", $dayOfWeek)
            ->where("is_active", true)
            ->where("start_time", "<=", $time)
            ->where("end_time", ">", $time)
            ->exists();

        if (! $schedule) {
            $validator->errors()->add(
                "appointment_time",
                "La hora seleccionada está fuera del horario de atención del veterinario para ese día."
            );
        }

        $conflict = Cita::where("veterinarian_id", $vet)
            ->where("appointment_date", $date)
            ->where("appointment_time", $time)
            ->where("id", "!=", $this->cita->id ?? 0)
            ->whereIn("status", ["pendiente", "confirmada"])
            ->exists();

        if ($conflict) {
            $validator->errors()->add(
                "appointment_time",
                "El veterinario ya tiene una cita a esa hora."
            );
        }
    }
}