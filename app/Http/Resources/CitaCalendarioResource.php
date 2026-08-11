<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaCalendarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $horaInicio = $this->appointment_time?->format("H:i");
        $duracion = (int) ($this->service?->duration_minutes ?? 30);
        $horaFin = $this->appointment_time?->copy()->addMinutes($duracion);

        $vetPalette = [
            "#0ea5e9",
            "#8b5cf6",
            "#f43f5e",
            "#10b981",
            "#f59e0b",
            "#14b8a6",
            "#6366f1",
            "#ec4899",
            "#84cc16",
            "#f97316",
        ];
        $colorVet = $vetPalette[($this->veterinarian_id ?? 0) % count($vetPalette)];

        return [
            "id" => (string) $this->id,
            "title" => $this->paciente?->name ?? "Sin mascota",
            "start" => $this->appointment_date?->format("Y-m-d") .
                "T" .
                $horaInicio,
            "end" => $this->appointment_date?->format("Y-m-d") .
                "T" .
                ($horaFin?->format("H:i") ?? $horaInicio),
            "allDay" => false,
            "color" => $colorVet,
            "extendedProps" => [
                "status" => $this->status,
                "status_label" => $this->status ? ucfirst($this->status) : "—",
                "veterinarian" => $this->veterinarian?->username ?? "—",
                "veterinarian_id" => $this->veterinarian_id,
                "vet_color" => $colorVet,
                "service" => $this->service?->name ?? "—",
                "reason" => $this->reason ?? "—",
                "cost" => $this->service?->base_price ?? 0,
                "day" => $this->appointment_date?->locale("es")->isoFormat("dddd") ?? "—",
                "hora_atencion" => $horaInicio . " – " . ($horaFin?->format("H:i") ?? $horaInicio),
                "notes" => $this->medical_records?->first()?->notes ?? "—",
                "pet" => [
                    "name" => $this->paciente?->name ?? "—",
                    "species" => $this->paciente?->species?->name ?? "—",
                    "breed" => $this->paciente?->breed?->name ?? "—",
                    "owner" => $this->paciente?->owner
                        ? trim(
                            $this->paciente->owner->first_name .
                                " " .
                                $this->paciente->owner->last_name,
                        )
                        : "—",
                    "phone" => $this->paciente?->owner?->phone ?? "—",
                ],
                "edit_url" => route("admin.citas.edit", $this->id),
            ],
        ];
    }
}
