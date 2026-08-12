<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "pet_name" => $this->paciente?->name,
            "pet_photo" => $this->paciente?->photo,
            "species" => $this->paciente?->species?->name,
            "owner_name" => optional($this->paciente?->owner)->first_name . " " . optional($this->paciente?->owner)->last_name,
            "remindable_type" => $this->remindable_type,
            "remindable_id" => $this->remindable_id,
            "type_label" => $this->typeLabel(),
            "message" => $this->message,
            "status" => $this->status,
            "remind_at" => $this->remind_at?->format("Y-m-d H:i"),
            "created_at" => $this->created_at?->format("Y-m-d"),
        ];
    }

    private function typeLabel(): string
    {
        return match ($this->remindable_type) {
            "cita" => "Cita",
            "vacuna" => "Vacuna",
            "cirugia", "surgiere" => "Cirugía",
            default => ucfirst((string) $this->remindable_type),
        };
    }
}