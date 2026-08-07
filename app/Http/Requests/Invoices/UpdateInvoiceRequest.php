<?php

namespace App\Http\Requests\Invoices;

use App\Models\Cita;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInvoiceRequest extends FormRequest
{
    private const INVOICEABLE_MODELS = [
        "cita" => Cita::class,
        "vacuna" => Vacuna::class,
        "surgiere" => Surgiere::class,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "invoiceable_type" => ["required", "string", "in:cita,vacuna,surgiere"],
            "invoiceable_id" => ["required", "integer"],
            "owner_id" => ["required", "integer", "exists:owners,id"],
            "total" => ["required", "numeric", "min:0.01"],
            "issued_at" => ["required", "date"],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $type = $this->input("invoiceable_type");
                $id = $this->input("invoiceable_id");
                $model = self::INVOICEABLE_MODELS[$type] ?? null;

                if ($type && $id && (! $model || ! $model::whereKey($id)->exists())) {
                    $validator->errors()->add("invoiceable_id", "El servicio origen seleccionado no existe.");
                }
            },
        ];
    }
}