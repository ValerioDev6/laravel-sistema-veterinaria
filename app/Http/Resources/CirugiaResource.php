<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CirugiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "pet_name" => $this->paciente?->name,
            "veterinarian_id" => $this->veterinarian_id,
            "veterinarian" => $this->user?->username,
            "cita_id" => $this->cita_id,
            "surgery_type" => $this->surgery_type,
            "surgery_date" => $this->surgery_date?->format("Y-m-d H:i"),
            "outcome" => $this->outcome,
            "status" => $this->status,
            "medical_notes" => $this->medical_notes,
            "edit_url" => route("admin.cirugias.edit", $this->id),
        ];
    }
}