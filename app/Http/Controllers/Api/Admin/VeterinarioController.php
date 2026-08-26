<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ToggleUserStatusAction;
use App\Actions\Users\UpdateUserAction;
use App\Actions\Veterinarios\ListVeterinariosAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Veterinarios\StoreVeterinarioRequest;
use App\Http\Requests\Veterinarios\UpdateVeterinarioRequest;
use App\Http\Resources\VeterinarioResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VeterinarioController extends Controller
{
    public function index(Request $request, ListVeterinariosAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => VeterinarioResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function store(StoreVeterinarioRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $datos["role"] = "Veterinario";

        $user = CreateUserAction::execute($datos);

        return response()->json([
            "status" => true,
            "message" => "Veterinario creado correctamente",
            "data" => new VeterinarioResource($user->load(["branch", "veterinarian_schedules"])),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateVeterinarioRequest $request,
        User $user,
    ): JsonResponse {
        abort_unless($user->hasRole("Veterinario"), 404);

        $datos = $request->validated();
        $datos["role"] = "Veterinario";

        $user = UpdateUserAction::execute($user, $datos);

        return response()->json([
            "status" => true,
            "message" => "Veterinario actualizado correctamente",
            "data" => new VeterinarioResource($user->load(["branch", "veterinarian_schedules"])),
            "errors" => (object) [],
        ]);
    }

    public function toggleStatus(
        User $user,
        ToggleUserStatusAction $action
    ): JsonResponse {
        abort_unless($user->hasRole("Veterinario"), 404);

        $user = $action->execute($user);

        return response()->json([
            "status" => true,
            "message" => $user->is_active
                ? "Veterinario activado"
                : "Veterinario desactivado",
            "data" => new VeterinarioResource($user),
            "errors" => (object) [],
        ]);
    }

    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        abort_unless($user->hasRole("Veterinario"), 404);

        $action->execute($user);

        return response()->json([
            "status" => true,
            "message" => "Veterinario eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
