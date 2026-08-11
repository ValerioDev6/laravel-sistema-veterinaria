<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CirugiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $invoice = $this->invoice;

        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "pet_name" => $this->paciente?->name,
            "species_id" => $this->paciente?->species_id,
            "species" => $this->paciente?->species?->name,
            "veterinarian_id" => $this->veterinarian_id,
            "veterinarian" => $this->user?->username,
            "cita_id" => $this->cita_id,
            "surgery_type" => $this->surgery_type,
            "surgery_date" => $this->surgery_date?->format("Y-m-d H:i"),
            "surgery_time" => $this->surgery_date?->format("H:i"),
            "outcome" => $this->outcome,
            "status" => $this->status,
            "medical_notes" => $this->medical_notes,
            "payment_status" => $invoice?->status,
            "payment_total" => $invoice?->total,
            "payment_paid" => $invoice?->payments?->sum("amount") ?? 0,
            "edit_url" => route("admin.cirugias.edit", $this->id),
        ];
    }
}
