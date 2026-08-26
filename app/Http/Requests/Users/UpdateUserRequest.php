<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $horarios = json_decode($this->input("schedules") ?? null, true);

        $this->merge([
            "schedules" => is_array($horarios) ? $horarios : null,
        ]);
    }

    public function rules(): array
    {
        return [
            "username" => [
                "required",
                "string",
                "max:255",
                "unique:users,username,{$this->user->id}",
            ],
            "email" => [
                "required",
                "email",
                "max:255",
                "unique:users,email,{$this->user->id}",
            ],
            "password" => ["nullable", "string", "min:8", "confirmed"],
            "phone" => ["nullable", "string", "max:20"],
            "type_documento" => ["nullable", "string", "in:DNI,CE,Pasaporte,RUC"],
            "n_documento" => ["nullable", "string", "max:50"],
            "birthday" => ["nullable", "date"],
            "branch_id" => ["nullable", "integer", "exists:branches,id"],
            "role" => ["required", "string", "exists:roles,name"],
            "avatar" => ["nullable", "image", "max:2048"],
            "schedules" => ["nullable", "array"],
            "schedules.*.day_of_week" => ["required", "integer", "between:0,6"],
            "schedules.*.start_time" => ["required", "date_format:H:i"],
            "schedules.*.end_time" => [
                "required",
                "date_format:H:i",
                "after:schedules.*.start_time",
            ],
        ];
    }
}