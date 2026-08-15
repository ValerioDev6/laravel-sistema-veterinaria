<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Permisos\ListPermissionsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request, ListPermissionsAction $action): JsonResponse
    {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => PermissionResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }
}
