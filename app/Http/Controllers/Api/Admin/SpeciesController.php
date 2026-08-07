<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Species\CreateSpeciesAction;
use App\Actions\Species\DeleteSpeciesAction;
use App\Actions\Species\ListSpeciesAction;
use App\Actions\Species\UpdateSpeciesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Species\StoreSpeciesRequest;
use App\Http\Requests\Species\UpdateSpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Species;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    public function index(Request $request, ListSpeciesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => SpeciesResource::collection($paginated->items()),
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
        StoreSpeciesRequest $request,
        CreateSpeciesAction $action
    ): JsonResponse {
        $species = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Especie creada correctamente",
            "data" => new SpeciesResource($species),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateSpeciesRequest $request,
        Species $species,
        UpdateSpeciesAction $action
    ): JsonResponse {
        $species = $action->execute($species, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Especie actualizada correctamente",
            "data" => new SpeciesResource($species),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Species $species,
        DeleteSpeciesAction $action
    ): JsonResponse {
        $action->execute($species);

        return response()->json([
            "status" => true,
            "message" => "Especie eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}