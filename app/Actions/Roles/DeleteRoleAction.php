<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function execute(Role $role): void
    {
        if ($role->name === "Super-Admin") {
            throw ValidationException::withMessages([
                "name" => "El rol Super-Admin no se puede eliminar.",
            ]);
        }

        $usersCount = DB::table("model_has_roles")
            ->where("role_id", $role->id)
            ->count();

        if ($usersCount > 0) {
            throw ValidationException::withMessages([
                "name" => "No se puede eliminar el rol \"{$role->name}\" porque tiene {$usersCount} usuario(s) asignado(s).",
            ]);
        }

        $role->delete();
    }
}
