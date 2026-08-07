<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "medical_record_id" => $this->medical_record_id,
            "file_url" => $this->file_url,
            "file_type" => $this->file_type,
            "created_at" => $this->created_at?->format("Y-m-d H:i"),
        ];
    }
}