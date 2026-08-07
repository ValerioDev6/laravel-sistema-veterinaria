<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "category" => $this->category,
            "base_price" => (float) $this->base_price,
            "duration_minutes" => $this->duration_minutes,
            "edit_url" => route("admin.services.edit", $this->id),
        ];
    }
}