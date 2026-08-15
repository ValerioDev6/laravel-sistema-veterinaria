<?php

namespace App\Actions\Roles;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UpdateRoleAction
{
    public function execute(Role $role, array $data): Role
    {
        $role->update([
            "name" => $data["name"],
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
