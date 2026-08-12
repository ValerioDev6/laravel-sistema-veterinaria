<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Reminders\ListarNotificacionesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use Illuminate\Http\JsonResponse;

class NotificacionController extends Controller
{
    public function index(
        ListarNotificacionesAction $action,
    ): JsonResponse {
        $result = $action->execute();

        return response()->json([
            "success" => true,
            "count" => $result["count"],
            "data" => ReminderResource::collection($result["items"]),
        ]);
    }
}