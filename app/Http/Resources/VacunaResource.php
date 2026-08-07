<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacunaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "pet_name" => $this->paciente?->name,
            "veterinarian_id" => $this->veterinarian_id,
            "veterinarian" => $this->user?->username,
            "vaccine_type_id" => $this->vaccine_type_id,
            "vaccine_type" => $this->vaccine_type?->name,
            "cita_id" => $this->cita_id,
            "vaccination_date" => $this->vaccination_date?->format("Y-m-d"),
            "next_due_date" => $this->next_due_date?->format("Y-m-d"),
            "edit_url" => route("admin.vacunas.edit", $this->id),
        ];
    }
}