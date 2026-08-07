<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalSignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pet_id" => $this->pet_id,
            "medical_record_id" => $this->medical_record_id,
            "weight" => $this->weight,
            "temperature" => $this->temperature,
            "heart_rate" => $this->heart_rate,
            "recorded_at" => $this->recorded_at?->format("Y-m-d H:i"),
        ];
    }
}