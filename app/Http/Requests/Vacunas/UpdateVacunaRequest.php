<?php

namespace App\Http\Requests\Vacunas;

use App\Actions\Vacunas\ValidarDisponibilidadVacuna;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateVacunaRequest extends FormRequest
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
            "vaccine_type_id" => [
                "required_without:new_vaccine_type",
                "nullable",
                "integer",
                "exists:vaccine_types,id",
            ],
            "new_vaccine_type" => [
                "required_without:vaccine_type_id",
                "nullable",
                "array",
            ],
            "new_vaccine_type.name" => [
                "required_with:new_vaccine_type",
                "string",
                "max:100",
            ],
            "new_vaccine_type.base_price" => [
                "required_with:new_vaccine_type",
                "numeric",
                "min:0",
            ],
            "new_vaccine_type.species_id" => [
                "nullable",
                "integer",
                "exists:species,id",
            ],
            "cita_id" => ["nullable", "integer", "exists:citas,id"],
            "vaccination_date" => [
                "required",
                "date",
                function ($attribute, $value, $fail) {
                    $original = $this->route("vacuna")?->vaccination_date?->format(
                        "Y-m-d",
                    );
                    if (
                        $value < now()->toDateString() &&
                        $value !== $original
                    ) {
                        $fail(
                            "The vaccination date field must be today or a future date.",
                        );
                    }
                },
            ],
            "vaccination_time" => ["required", "date_format:H:i"],
            "next_due_date" => ["nullable", "date", "after_or_equal:vaccination_date"],
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
                if (! $validator->errors()->isNotEmpty()) {
                    try {
                        ValidarDisponibilidadVacuna::execute(
                            $this->validated(),
                            $this->route("vacuna")?->id,
                        );
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $validator->errors()->add(
                            "vaccination_time",
                            $e->errors()["vaccination_time"][0],
                        );
                    }
                }
            },
        ];
    }
}