<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeterinarianScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "veterinarian_id" => $this->veterinarian_id,
            "veterinarian" => $this->user?->username,
            "day_of_week" => $this->day_of_week,
            "day_label" => $this->day_label,
            "start_time" => $this->start_time?->format("H:i"),
            "end_time" => $this->end_time?->format("H:i"),
            "is_active" => (bool) $this->is_active,
            "edit_url" => route("admin.veterinarian-schedules.edit", $this->id),
        ];
    }
}