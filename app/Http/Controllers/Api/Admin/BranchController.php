<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Branches\CreateBranchAction;
use App\Actions\Branches\DeleteBranchAction;
use App\Actions\Branches\ListBranchesAction;
use App\Actions\Branches\UpdateBranchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Branches\StoreBranchRequest;
use App\Http\Requests\Branches\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request, ListBranchesAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => BranchResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" =>
                    $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function show(Branch $branch): JsonResponse
    {
        return response()->json([
            "status" => true,
            "message" => "Detalle de sucursal",
            "data" => new BranchResource($branch),
            "errors" => (object) [],
        ]);
    }

    public function store(
        StoreBranchRequest $request,
        CreateBranchAction $action,
    ): JsonResponse {
        $branch = $action->execute($request->validated());

        return response()->json(
            [
                "status" => true,
                "message" => "Sucursal creada correctamente",
                "data" => new BranchResource($branch),
                "errors" => (object) [],
            ],
            201,
        );
    }

    public function update(
        UpdateBranchRequest $request,
        Branch $branch,
        UpdateBranchAction $action,
    ): JsonResponse {
        $branch = $action->execute($branch, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Sucursal actualizada correctamente",
            "data" => new BranchResource($branch),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Branch $branch,
        DeleteBranchAction $action,
    ): JsonResponse {
        $action->execute($branch);

        return response()->json([
            "status" => true,
            "message" => "Sucursal eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
