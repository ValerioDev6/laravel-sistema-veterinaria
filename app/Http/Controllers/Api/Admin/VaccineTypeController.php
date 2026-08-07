<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\VaccineTypes\CreateVaccineTypeAction;
use App\Actions\VaccineTypes\DeleteVaccineTypeAction;
use App\Actions\VaccineTypes\ListVaccineTypesAction;
use App\Actions\VaccineTypes\UpdateVaccineTypeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\VaccineTypes\StoreVaccineTypeRequest;
use App\Http\Requests\VaccineTypes\UpdateVaccineTypeRequest;
use App\Http\Resources\VaccineTypeResource;
use App\Models\VaccineType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccineTypeController extends Controller
{
    public function index(Request $request, ListVaccineTypesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => VaccineTypeResource::collection($paginated->items()),
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
        StoreVaccineTypeRequest $request,
    ): JsonResponse {
        $vaccineType = CreateVaccineTypeAction::execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Tipo de vacuna creado correctamente",
            "data" => new VaccineTypeResource($vaccineType->load("species")),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateVaccineTypeRequest $request,
        VaccineType $vaccineType,
    ): JsonResponse {
        $vaccineType = UpdateVaccineTypeAction::execute($vaccineType, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Tipo de vacuna actualizado correctamente",
            "data" => new VaccineTypeResource($vaccineType->load("species")),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        VaccineType $vaccineType,
        DeleteVaccineTypeAction $action
    ): JsonResponse {
        $action->execute($vaccineType);

        return response()->json([
            "status" => true,
            "message" => "Tipo de vacuna eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}