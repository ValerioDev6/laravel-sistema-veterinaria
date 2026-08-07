<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Medicines\CreateMedicineAction;
use App\Actions\Medicines\DeleteMedicineAction;
use App\Actions\Medicines\ListMedicinesAction;
use App\Actions\Medicines\UpdateMedicineAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medicines\StoreMedicineRequest;
use App\Http\Requests\Medicines\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request, ListMedicinesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => MedicineResource::collection($paginated->items()),
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
        StoreMedicineRequest $request,
        CreateMedicineAction $action
    ): JsonResponse {
        $medicine = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Medicamento creado correctamente",
            "data" => new MedicineResource($medicine),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateMedicineRequest $request,
        Medicine $medicine,
        UpdateMedicineAction $action
    ): JsonResponse {
        $medicine = $action->execute($medicine, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Medicamento actualizado correctamente",
            "data" => new MedicineResource($medicine),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Medicine $medicine,
        DeleteMedicineAction $action
    ): JsonResponse {
        $action->execute($medicine);

        return response()->json([
            "status" => true,
            "message" => "Medicamento eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}