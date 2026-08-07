<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "medicine_id" => $this->medicine_id,
            "medicine" => $this->medicine?->name,
            "dosage" => $this->dosage,
            "duration_days" => $this->duration_days,
        ];
    }
}