<?php

namespace App\Http\Resources;

use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "guard_name" => $this->guard_name,
            "grupo" => PermissionCatalog::groupFor($this->name),
            "label" => PermissionCatalog::labelFor($this->name),
            "roles_count" => $this->whenCounted("roles"),
        ];
    }
}
