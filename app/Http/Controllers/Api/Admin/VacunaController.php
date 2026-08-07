<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Vacunas\CreateVacunaAction;
use App\Actions\Vacunas\DeleteVacunaAction;
use App\Actions\Vacunas\ListVacunasAction;
use App\Actions\Vacunas\UpdateVacunaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vacunas\StoreVacunaRequest;
use App\Http\Requests\Vacunas\UpdateVacunaRequest;
use App\Http\Resources\VacunaResource;
use App\Models\Vacuna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacunaController extends Controller
{
    public function index(
        Request $request,
        ListVacunasAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => VacunaResource::collection($paginated->items()),
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
        StoreVacunaRequest $request,
        CreateVacunaAction $action
    ): JsonResponse {
        $vacuna = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Vacuna registrada correctamente",
            "data" => new VacunaResource($vacuna->load(["paciente", "user", "vaccine_type"])),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateVacunaRequest $request,
        Vacuna $vacuna,
        UpdateVacunaAction $action
    ): JsonResponse {
        $vacuna = $action->execute($vacuna, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Vacuna actualizada correctamente",
            "data" => new VacunaResource($vacuna->load(["paciente", "user", "vaccine_type"])),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Vacuna $vacuna,
        DeleteVacunaAction $action
    ): JsonResponse {
        $action->execute($vacuna);

        return response()->json([
            "status" => true,
            "message" => "Vacuna eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}