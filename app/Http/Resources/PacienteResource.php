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
            "owner_first_name" => $this->owner?->first_name,
            "owner_last_name" => $this->owner?->last_name,
            "owner_email" => $this->owner?->email,
            "owner_phone" => $this->owner?->phone,
            "owner_address" => $this->owner?->address,
            "owner_city" => $this->owner?->city,
            "owner_type_documento" => $this->owner?->type_documento,
            "owner_n_documento" => $this->owner?->n_documento,
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