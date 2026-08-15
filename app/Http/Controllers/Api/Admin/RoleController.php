<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Roles\CreateRoleAction;
use App\Actions\Roles\DeleteRoleAction;
use App\Actions\Roles\ListRolesAction;
use App\Actions\Roles\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, ListRolesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => RoleResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function store(StoreRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        $role = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Rol creado correctamente",
            "data" => new RoleResource($role->loadCount(["users", "permissions"])),
            "errors" => (object) [],
        ], 201);
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): JsonResponse
    {
        $role = $action->execute($role, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Rol actualizado correctamente",
            "data" => new RoleResource($role->loadCount(["users", "permissions"])),
            "errors" => (object) [],
        ]);
    }

    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $action->execute($role);

        return response()->json([
            "status" => true,
            "message" => "Rol eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
