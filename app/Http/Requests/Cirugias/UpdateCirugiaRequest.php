<?php

namespace App\Http\Requests\Cirugias;

use App\Actions\Cirugias\ValidarDisponibilidadCirugia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCirugiaRequest extends FormRequest
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
            "cita_id" => ["nullable", "integer", "exists:citas,id"],
            "surgery_type" => ["nullable", "string", "max:100"],
            "surgery_date" => ["required", "date"],
            "surgery_time" => ["required", "date_format:H:i"],
            "outcome" => ["nullable", "string"],
            "status" => ["required", "string", "in:pendiente,en_proceso,completada,cancelada"],
            "medical_notes" => ["nullable", "string"],
            "total" => ["required", "numeric", "min:0"],
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
                        $param = $this->route("cirugia");
                        $id = is_object($param) ? $param->id : (int) $param;

                        ValidarDisponibilidadCirugia::execute(
                            $this->validated(),
                            $id ?: null,
                        );
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $validator->errors()->add(
                            "surgery_time",
                            $e->errors()["surgery_time"][0],
                        );
                    }
                }
            },
        ];
    }
}