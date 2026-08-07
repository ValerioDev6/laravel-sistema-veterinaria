<?php

namespace App\Http\Requests\VeterinarianSchedules;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "veterinarian_id" => ["required", "integer", "exists:users,id"],
            "day_of_week" => ["required", "integer", "between:0,6"],
            "start_time" => ["required", "date_format:H:i"],
            "end_time" => ["required", "date_format:H:i", "after:start_time"],
            "is_active" => ["boolean"],
        ];
    }
}