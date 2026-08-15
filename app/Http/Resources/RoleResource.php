<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "guard_name" => $this->guard_name,
            "permissions_count" => $this->whenCounted("permissions"),
            "users_count" => $this->whenCounted("users"),
            "is_super_admin" => $this->name === "Super-Admin",
            "edit_url" => route("admin.roles.edit", $this->id),
        ];
    }
}
