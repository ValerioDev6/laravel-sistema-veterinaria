<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "pet_name" => $this->paciente?->name,
            "veterinarian_id" => $this->veterinarian_id,
            "veterinarian" => $this->veterinarian?->username,
            "created_by_user_id" => $this->created_by_user_id,
            "created_by" => $this->user?->username,
            "service_id" => $this->service_id,
            "service" => $this->service?->name,
            "appointment_date" => $this->appointment_date?->format("Y-m-d"),
            "appointment_time" => $this->appointment_time?->format("H:i"),
            "reason" => $this->reason,
            "reprogramming" => (bool) $this->reprogramming,
            "status" => $this->status,
            "edit_url" => route("admin.citas.edit", $this->id),
        ];
    }
}