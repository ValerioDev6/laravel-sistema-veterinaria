<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "branch_id" => $this->branch_id,
            "branch" => $this->whenLoaded("branch", fn () => $this->branch?->name),
            "username" => $this->username,
            "email" => $this->email,
            "phone" => $this->phone,
            "type_documento" => $this->type_documento,
            "n_documento" => $this->n_documento,
            "birthday" => $this->birthday?->format("Y-m-d"),
            "avatar" => $this->avatar,
            "is_active" => (bool) $this->is_active,
            "roles" => $this->getRoleNames()->values(),
            "edit_url" => route("admin.usuarios.edit", $this->id),
            "movimientos_url" => route("admin.usuarios.movimientos", $this->id),
            "created_at" => $this->created_at,
        ];
    }
}