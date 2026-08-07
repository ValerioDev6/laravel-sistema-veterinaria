<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
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
            "vaccination_id" => $this->vaccination_id,
            "surgery_id" => $this->surgery_id,
            "event_type" => $this->event_type,
            "event_date" => $this->event_date?->format("Y-m-d"),
            "notes" => $this->notes,
            "prescriptions" => PrescriptionResource::collection($this->whenLoaded("prescriptions")),
            "vital_signs" => VitalSignResource::collection($this->whenLoaded("vital_signs")),
            "attachments" => MedicalRecordAttachmentResource::collection($this->whenLoaded("medical_record_attachments")),
            "show_url" => route("admin.medical-records.show", $this->id),
        ];
    }
}