<?php

namespace App\Actions\Roles;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CreateRoleAction
{
    public function execute(array $data): Role
    {
        $role = Role::create([
            "name" => $data["name"],
            "guard_name" => "api",
        ]);

        $role->syncPermissions($this->permissionIds($data));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role;
    }

    /**
     * @return array<int, int>
     */
    protected function permissionIds(array $data): array
    {
        $ids = $data["permissions"] ?? [];

        return Permission::whereIn("id", $ids)
            ->where("guard_name", "api")
            ->pluck("id")
            ->all();
    }
}
