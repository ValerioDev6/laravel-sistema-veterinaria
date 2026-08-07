<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BreedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "species_id" => $this->species_id,
            "species" => $this->species?->name,
            "name" => $this->name,
            "edit_url" => route("admin.breeds.edit", $this->id),
        ];
    }
}