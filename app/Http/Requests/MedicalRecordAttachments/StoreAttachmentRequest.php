<?php

namespace App\Http\Requests\MedicalRecordAttachments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "file" => ["required", "file", "max:5120", "mimes:jpeg,png,jpg,webp,pdf"],
        ];
    }
}