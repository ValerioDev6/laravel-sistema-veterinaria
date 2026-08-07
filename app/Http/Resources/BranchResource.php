<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "address" => $this->address,
            "city" => $this->city,
            "phone" => $this->phone,
            "created_at" => $this->created_at,
            "edit_url" => route("admin.branches.edit", $this->id),
        ];
    }
}