<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Services\CreateServiceAction;
use App\Actions\Services\DeleteServiceAction;
use App\Actions\Services\ListServicesAction;
use App\Actions\Services\UpdateServiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request, ListServicesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => ServiceResource::collection($paginated->items()),
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
        StoreServiceRequest $request,
    ): JsonResponse {
        $service = CreateServiceAction::execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Servicio creado correctamente",
            "data" => new ServiceResource($service),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateServiceRequest $request,
        Service $service,
    ): JsonResponse {
        $service = UpdateServiceAction::execute($service, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Servicio actualizado correctamente",
            "data" => new ServiceResource($service),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Service $service,
        DeleteServiceAction $action
    ): JsonResponse {
        $action->execute($service);

        return response()->json([
            "status" => true,
            "message" => "Servicio eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}