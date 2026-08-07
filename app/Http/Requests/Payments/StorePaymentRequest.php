<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "amount" => ["required", "numeric", "min:0.01"],
            "advance_amount" => ["nullable", "numeric", "min:0"],
            "payment_method" => ["required", "string", "in:efectivo,tarjeta,transferencia,otro"],
            "paid_at" => ["required", "date"],
        ];
    }
}