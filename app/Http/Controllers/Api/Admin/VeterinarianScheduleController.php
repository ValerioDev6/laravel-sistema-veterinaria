<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\VeterinarianSchedules\CreateScheduleAction;
use App\Actions\VeterinarianSchedules\DeleteScheduleAction;
use App\Actions\VeterinarianSchedules\ListVeterinarianSchedulesAction;
use App\Actions\VeterinarianSchedules\UpdateScheduleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\VeterinarianSchedules\StoreScheduleRequest;
use App\Http\Requests\VeterinarianSchedules\UpdateScheduleRequest;
use App\Http\Resources\VeterinarianScheduleResource;
use App\Models\VeterinarianSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VeterinarianScheduleController extends Controller
{
    public function index(
        Request $request,
        ListVeterinarianSchedulesAction $action,
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => VeterinarianScheduleResource::collection(
                $paginated->items(),
            ),
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

    public function store(
        StoreScheduleRequest $request,
        CreateScheduleAction $action,
    ): JsonResponse {
        $schedule = $action->execute($request->validated());

        return response()->json(
            [
                "status" => true,
                "message" => "Horario creado correctamente",
                "data" => new VeterinarianScheduleResource(
                    $schedule->load("user"),
                ),
                "errors" => (object) [],
            ],
            201,
        );
    }

    public function update(
        UpdateScheduleRequest $request,
        VeterinarianSchedule $veterinarianSchedule,
        UpdateScheduleAction $action,
    ): JsonResponse {
        $schedule = $action->execute(
            $veterinarianSchedule,
            $request->validated(),
        );

        return response()->json([
            "status" => true,
            "message" => "Horario actualizado correctamente",
            "data" => new VeterinarianScheduleResource($schedule->load("user")),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        VeterinarianSchedule $veterinarianSchedule,
        DeleteScheduleAction $action,
    ): JsonResponse {
        $action->execute($veterinarianSchedule);

        return response()->json([
            "status" => true,
            "message" => "Horario eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
