<?php

namespace App\Http\Requests\Citas;

use App\Models\Cita;
use App\Models\VeterinarianSchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCitaRequest extends FormRequest
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
            "service_id" => [
                "required_without:new_service",
                "nullable",
                "integer",
                "exists:services,id",
            ],
            "new_service" => ["required_without:service_id", "nullable", "array"],
            "new_service.name" => ["required_with:new_service", "string", "max:100"],
            "new_service.category" => [
                "required_with:new_service",
                "in:consulta,vacunacion,cirugia,estetica,otro",
            ],
            "new_service.base_price" => [
                "required_with:new_service",
                "numeric",
                "min:0",
            ],
            "new_service.duration_minutes" => [
                "required_with:new_service",
                "integer",
                "min:5",
                "max:600",
            ],
            "appointment_date" => ["required", "date", "after_or_equal:today"],
            "appointment_time" => ["required", "date_format:H:i"],
            "reason" => ["nullable", "string"],
            "reminder_date" => ["nullable", "date"],
            "reprogramming" => ["boolean"],
            "status" => ["nullable", "string", "in:pendiente,confirmada,completada,cancelada"],
            "payment_method" => [
                "required",
                "string",
                "in:efectivo,tarjeta,transferencia,otro",
            ],
            "advance_amount" => ["required", "numeric", "min:0.01"],
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
