<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Reminders\CambiarEstadoReminderAction;
use App\Actions\Reminders\ListRemindersAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reminders\UpdateReminderRequest;
use App\Http\Resources\ReminderResource;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(
        Request $request,
        ListRemindersAction $action,
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => ReminderResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function cambiarEstado(
        UpdateReminderRequest $request,
        Reminder $reminder,
        CambiarEstadoReminderAction $action,
    ): JsonResponse {
        $reminder = $action->execute($reminder, $request->validated()["status"]);

        return response()->json([
            "status" => true,
            "message" => "Estado del recordatorio actualizado",
            "data" => new ReminderResource($reminder->load("paciente")),
            "errors" => (object) [],
        ]);
    }
}