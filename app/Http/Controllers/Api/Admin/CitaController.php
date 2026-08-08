<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Citas\CambiarEstadoCitaAction;
use App\Actions\Citas\CreateCitaAction;
use App\Actions\Citas\DeleteCitaAction;
use App\Actions\Citas\ListCitasAction;
use App\Actions\Citas\UpdateCitaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Citas\StoreCitaRequest;
use App\Http\Requests\Citas\UpdateCitaRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitaController extends Controller
{



    public function index(
        Request $request,
        ListCitasAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => CitaResource::collection($paginated->items()),
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
        StoreCitaRequest $request,
    ): JsonResponse {
        $cita = CreateCitaAction::execute($request->validated());

        return response()->json(
            [
                "status" => true,
                "message" => "Cita creada correctamente",
                "data" => new CitaResource(
                    $cita->load(["paciente", "veterinarian", "service"]),
                ),
                "errors" => (object) [],
            ],
            201,
        );
    }

    public function update(
        UpdateCitaRequest $request,
        Cita $cita,
    ): JsonResponse {
        $cita = UpdateCitaAction::execute($cita, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Cita actualizada correctamente",
            "data" => new CitaResource(
                $cita->load(["paciente", "veterinarian", "service"]),
            ),
            "errors" => (object) [],
        ]);
    }

    public function cambiarEstado(
        Cita $cita,
        CambiarEstadoCitaAction $action,
    ): JsonResponse {
        $request = request()->validate([
            "status" => [
                "required",
                "string",
                "in:pendiente,confirmada,completada,cancelada",
            ],
        ]);

        $cita = $action->execute($cita, $request["status"]);

        return response()->json([
            "status" => true,
            "message" => "Estado de la cita actualizado",
            "data" => new CitaResource(
                $cita->load(["paciente", "veterinarian", "service"]),
            ),
            "errors" => (object) [],
        ]);
    }

    public function destroy(Cita $cita, DeleteCitaAction $action): JsonResponse
    {
        $action->execute($cita);

        return response()->json([
            "status" => true,
            "message" => "Cita eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}
