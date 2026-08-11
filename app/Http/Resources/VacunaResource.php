<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacunaResource extends JsonResource
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
            "vaccine_type_id" => $this->vaccine_type_id,
            "vaccine_type" => $this->vaccine_type?->name,
            "vaccine_price" => $this->vaccine_type?->base_price,
            "cita_id" => $this->cita_id,
            "vaccination_date" => $this->vaccination_date?->format("Y-m-d"),
            "vaccination_time" => $this->vaccination_time?->format("H:i"),
            "next_due_date" => $this->next_due_date?->format("Y-m-d"),
            "payment_status" => $invoice?->status,
            "payment_total" => $invoice?->total,
            "payment_paid" => $invoice?->payments?->sum("amount") ?? 0,
            "edit_url" => route("admin.vacunas.edit", $this->id),
        ];
    }
}