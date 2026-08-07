<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\ToggleUserStatusAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, ListUsersAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => UserResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function store(
        StoreUserRequest $request,
    ): JsonResponse {
        $user = CreateUserAction::execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Usuario creado correctamente",
            "data" => new UserResource($user->load("branch")),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
    ): JsonResponse {
        $user = UpdateUserAction::execute($user, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Usuario actualizado correctamente",
            "data" => new UserResource($user->load("branch")),
            "errors" => (object) [],
        ]);
    }

    public function toggleStatus(
        User $user,
        ToggleUserStatusAction $action
    ): JsonResponse {
        $user = $action->execute($user);

        return response()->json([
            "status" => true,
            "message" => $user->is_active
                ? "Usuario activado"
                : "Usuario desactivado",
            "data" => new UserResource($user),
            "errors" => (object) [],
        ]);
    }

    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $action->execute($user);

        return response()->json([
            "status" => true,
            "message" => "Usuario eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}