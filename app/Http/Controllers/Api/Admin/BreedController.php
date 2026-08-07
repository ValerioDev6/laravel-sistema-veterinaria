<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Breeds\CreateBreedAction;
use App\Actions\Breeds\DeleteBreedAction;
use App\Actions\Breeds\ListBreedsAction;
use App\Actions\Breeds\UpdateBreedAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Breeds\StoreBreedRequest;
use App\Http\Requests\Breeds\UpdateBreedRequest;
use App\Http\Resources\BreedResource;
use App\Models\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    public function index(
        Request $request,
        ListBreedsAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => BreedResource::collection($paginated->items()),
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
        StoreBreedRequest $request,
        CreateBreedAction $action
    ): JsonResponse {
        $breed = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Raza creada correctamente",
            "data" => new BreedResource($breed->load("species")),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdateBreedRequest $request,
        Breed $breed,
        UpdateBreedAction $action
    ): JsonResponse {
        $breed = $action->execute($breed, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Raza actualizada correctamente",
            "data" => new BreedResource($breed->load("species")),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Breed $breed,
        DeleteBreedAction $action
    ): JsonResponse {
        $action->execute($breed);

        return response()->json([
            "status" => true,
            "message" => "Raza eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}