<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaccineTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "base_price" => $this->base_price,
            "species_id" => $this->species_id,
            "species" => $this->species?->name,
            "edit_url" => route("admin.vaccine-types.edit", $this->id),
        ];
    }
}