<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Calendario\CalendarioAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CalendarioEventResource;
use Illuminate\Http\JsonResponse;

class CalendarioController extends Controller
{
    public function index(CalendarioAction $action): JsonResponse
    {
        $data = $action->execute();

        $eventos = collect()
            ->merge(
                $data["citas"]->map(
                    fn ($cita) => new CalendarioEventResource($cita, "cita"),
                ),
            )
            ->merge(
                $data["vacunas"]->map(
                    fn ($vacuna) => new CalendarioEventResource(
                        $vacuna,
                        "vacuna",
                    ),
                ),
            )
            ->merge(
                $data["cirugias"]->map(
                    fn ($cirugia) => new CalendarioEventResource(
                        $cirugia,
                        "cirugia",
                    ),
                ),
            )
            ->values();

        return response()->json([
            "success" => true,
            "data" => $eventos,
        ]);
    }
}
