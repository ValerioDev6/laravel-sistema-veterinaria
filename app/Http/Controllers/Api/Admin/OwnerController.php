<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Owners\CreateOwnerAction;
use App\Actions\Owners\DeleteOwnerAction;
use App\Actions\Owners\ListOwnersAction;
use App\Actions\Owners\UpdateOwnerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owners\StoreOwnerRequest;
use App\Http\Requests\Owners\UpdateOwnerRequest;
use App\Http\Resources\OwnerResource;
use App\Models\Owner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request, ListOwnersAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => OwnerResource::collection($paginated->items()),
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
        StoreOwnerRequest $request,
    ): JsonResponse {
        $owner = CreateOwnerAction::execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Propietario creado correctamente",
            "data" => new OwnerResource($owner),
            "errors" => (object) [],
        ], 201);
    }

    public function show(Owner $owner): JsonResponse
    {
        $owner->loadMissing("pacientes");

        return response()->json([
            "success" => true,
            "data" => new OwnerResource($owner),
        ]);
    }

    public function update(
        UpdateOwnerRequest $request,
        Owner $owner,
    ): JsonResponse {
        $owner = UpdateOwnerAction::execute($owner, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Propietario actualizado correctamente",
            "data" => new OwnerResource($owner),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Owner $owner,
        DeleteOwnerAction $action
    ): JsonResponse {
        $action->execute($owner);

        return response()->json([
            "status" => true,
            "message" => "Propietario eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
