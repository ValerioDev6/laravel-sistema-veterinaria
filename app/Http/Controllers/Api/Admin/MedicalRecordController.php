<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\MedicalRecords\CreateMedicalRecordAction;
use App\Actions\MedicalRecords\DeleteMedicalRecordAction;
use App\Actions\MedicalRecords\ListMedicalRecordsAction;
use App\Actions\MedicalRecords\UpdateMedicalRecordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecords\StoreMedicalRecordRequest;
use App\Http\Requests\MedicalRecords\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(
        Request $request,
        ListMedicalRecordsAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => MedicalRecordResource::collection($paginated->items()),
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
        StoreMedicalRecordRequest $request,
        CreateMedicalRecordAction $action
    ): JsonResponse {
        $record = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Entrada de historial creada correctamente",
            "data" => new MedicalRecordResource($record),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateMedicalRecordRequest $request,
        MedicalRecord $medicalRecord,
        UpdateMedicalRecordAction $action
    ): JsonResponse {
        $record = $action->execute($medicalRecord, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Entrada de historial actualizada correctamente",
            "data" => new MedicalRecordResource($record),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        MedicalRecord $medicalRecord,
        DeleteMedicalRecordAction $action
    ): JsonResponse {
        $action->execute($medicalRecord);

        return response()->json([
            "status" => true,
            "message" => "Entrada de historial eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}