<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Dashboard\ObtenerDashboardAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        Request $request,
        ObtenerDashboardAction $action,
    ): JsonResponse {
        $year = $request->integer("year") ?: null;

        return response()->json([
            "success" => true,
            "data" => $action->execute($year),
        ]);
    }
}
