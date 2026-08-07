<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PacienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "owner_id" => $this->owner_id,
            "owner_name" => $this->owner?->first_name . " " . $this->owner?->last_name,
            "species_id" => $this->species_id,
            "species" => $this->species?->name,
            "breed_id" => $this->breed_id,
            "breed" => $this->breed?->name,
            "birth_date" => $this->birth_date?->format("Y-m-d"),
            "gender" => $this->gender,
            "color" => $this->color,
            "weight" => $this->weight,
            "photo" => $this->photo,
            "medical_notes" => $this->medical_notes,
            "edit_url" => route("admin.pacientes.edit", $this->id),
            "show_url" => route("admin.pacientes.show", $this->id),
        ];
    }
}