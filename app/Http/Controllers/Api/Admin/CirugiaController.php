<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Cirugias\CambiarEstadoCirugiaAction;
use App\Actions\Cirugias\CambiarEstadoPagoCirugiaAction;
use App\Actions\Cirugias\CreateCirugiaAction;
use App\Actions\Cirugias\DeleteCirugiaAction;
use App\Actions\Cirugias\ListCirugiasAction;
use App\Actions\Cirugias\ObtenerDisponibilidadCirugiaAction;
use App\Actions\Cirugias\UpdateCirugiaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cirugias\StoreCirugiaRequest;
use App\Http\Requests\Cirugias\UpdateCirugiaRequest;
use App\Http\Resources\CirugiaResource;
use App\Models\Surgiere;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CirugiaController extends Controller
{
    public function index(
        Request $request,
        ListCirugiasAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => CirugiaResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function disponibilidad(
        Request $request,
        ObtenerDisponibilidadCirugiaAction $action,
    ): JsonResponse {
        $request->validate(["fecha" => ["required", "date"]]);

        return response()->json([
            "success" => true,
            "data" => $action->execute($request->input("fecha")),
        ]);
    }

    public function store(
        StoreCirugiaRequest $request,
    ): JsonResponse {
        $cirugia = CreateCirugiaAction::execute($request->validated());

        return response()->json(
            [
                "status" => true,
                "message" => "Cirugía registrada correctamente",
                "data" => new CirugiaResource(
                    $cirugia->load(["paciente", "user", "invoice", "invoice.payments"]),
                ),
                "errors" => (object) [],
            ],
            201,
        );
    }

    public function update(
        UpdateCirugiaRequest $request,
        Surgiere $cirugia,
    ): JsonResponse {
        $cirugia = UpdateCirugiaAction::execute($cirugia, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Cirugía actualizada correctamente",
            "data" => new CirugiaResource($cirugia->load(["paciente", "user", "invoice", "invoice.payments"])),
            "errors" => (object) [],
        ]);
    }

    public function cambiarEstado(
        Surgiere $cirugia,
        CambiarEstadoCirugiaAction $action,
    ): JsonResponse {
        $request = request()->validate([
            "status" => [
                "required",
                "string",
                "in:pendiente,en_proceso,completada,cancelada",
            ],
        ]);

        $cirugia = $action->execute($cirugia, $request["status"]);

        return response()->json([
            "status" => true,
            "message" => "Estado de la cirugía actualizado",
            "data" => new CirugiaResource($cirugia->load(["paciente", "user", "invoice", "invoice.payments"])),
            "errors" => (object) [],
        ]);
    }

    public function cambiarEstadoPago(
        Surgiere $cirugia,
        CambiarEstadoPagoCirugiaAction $action,
    ): JsonResponse {
        $request = request()->validate([
            "status" => [
                "required",
                "string",
                "in:pendiente,parcial,pagado,anulado",
            ],
        ]);

        $cirugia = $action->execute($cirugia, $request["status"]);

        return response()->json([
            "status" => true,
            "message" => "Estado de pago de la cirugía actualizado",
            "data" => new CirugiaResource(
                $cirugia->load(["paciente", "user", "invoice", "invoice.payments"]),
            ),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Surgiere $cirugia,
        DeleteCirugiaAction $action,
    ): JsonResponse {
        $action->execute($cirugia);
        return response()->json([
            "status" => true,
            "message" => "Cirugía eliminada correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}