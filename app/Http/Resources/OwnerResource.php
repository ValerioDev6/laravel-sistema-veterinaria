<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "full_name" => $this->first_name . " " . $this->last_name,
            "email" => $this->email,
            "phone" => $this->phone,
            "address" => $this->address,
            "city" => $this->city,
            "type_documento" => $this->type_documento,
            "n_documento" => $this->n_documento,
            "pacientes_count" => $this->pacientes_count ?? $this->pacientes()->count(),
            "pacientes" => PacienteResource::collection(
                $this->whenLoaded("pacientes"),
            ),
            "show_url" => route("admin.owners.show", $this->id),
        ];
    }
}