<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Pacientes\CreatePacienteAction;
use App\Actions\Pacientes\DeletePacienteAction;
use App\Actions\Pacientes\ListPacientesAction;
use App\Actions\Pacientes\UpdatePacienteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pacientes\StorePacienteRequest;
use App\Http\Requests\Pacientes\UpdatePacienteRequest;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(
        Request $request,
        ListPacientesAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => PacienteResource::collection($paginated->items()),
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
        StorePacienteRequest $request,
        CreatePacienteAction $action
    ): JsonResponse {
        $paciente = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Paciente creado correctamente",
            "data" => new PacienteResource($paciente->load(["owner", "species", "breed"])),
            "errors" => (object) [],
        ], 201);
    }

    public function update(
        UpdatePacienteRequest $request,
        Paciente $paciente,
        UpdatePacienteAction $action
    ): JsonResponse {
        $paciente = $action->execute($paciente, $request->validated());

        return response()->json([
            "status" => true,
            "message" => "Paciente actualizado correctamente",
            "data" => new PacienteResource($paciente->load(["owner", "species", "breed"])),
            "errors" => (object) [],
        ]);
    }

    public function destroy(
        Paciente $paciente,
        DeletePacienteAction $action
    ): JsonResponse {
        $action->execute($paciente);

        return response()->json([
            "status" => true,
            "message" => "Paciente eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }

    public function records(Paciente $paciente): JsonResponse
    {
        $records = $paciente->medical_records()
            ->with("veterinarian")
            ->orderByDesc("event_date")
            ->get()
            ->map(fn ($record) => [
                "id" => $record->id,
                "event_type" => $record->event_type,
                "event_date" => $record->event_date?->format("d/m/Y"),
                "veterinarian" => $record->veterinarian?->username,
                "notes" => $record->notes,
            ]);

        return response()->json([
            "status" => true,
            "message" => "Historial médico",
            "data" => $records,
            "errors" => (object) [],
        ]);
    }

    public function vacunas(Paciente $paciente): JsonResponse
    {
        $vacunas = $paciente->vacunas()
            ->with(["vaccine_type", "veterinarian"])
            ->orderByDesc("vaccination_date")
            ->get()
            ->map(fn ($vacuna) => [
                "id" => $vacuna->id,
                "vaccine_type" => $vacuna->vaccine_type?->name,
                "vaccination_date" => $vacuna->vaccination_date?->format("d/m/Y"),
                "next_due_date" => $vacuna->next_due_date?->format("d/m/Y"),
                "veterinarian" => $vacuna->veterinarian?->username,
            ]);

        return response()->json([
            "status" => true,
            "message" => "Vacunas",
            "data" => $vacunas,
            "errors" => (object) [],
        ]);
    }

    public function cirugias(Paciente $paciente): JsonResponse
    {
        $cirugias = $paciente->surgieres()
            ->with("veterinarian")
            ->orderByDesc("surgery_date")
            ->get()
            ->map(fn ($cirugia) => [
                "id" => $cirugia->id,
                "surgery_type" => $cirugia->surgery_type,
                "surgery_date" => $cirugia->surgery_date?->format("d/m/Y H:i"),
                "status" => $cirugia->status,
                "veterinarian" => $cirugia->veterinarian?->username,
                "outcome" => $cirugia->outcome,
            ]);

        return response()->json([
            "status" => true,
            "message" => "Cirugías",
            "data" => $cirugias,
            "errors" => (object) [],
        ]);
    }
}