<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarioEventResource extends JsonResource
{
    private const COLORES = [
        "cita" => "#405189",
        "vacuna" => "#10b981",
        "cirugia" => "#f59e0b",
    ];

    private const LABELES = [
        "cita" => "Cita",
        "vacuna" => "Vacuna",
        "cirugia" => "Cirugía",
    ];

    /**
     * @param mixed $resource  Modelo (Cita, Vacuna o Surgiere)
     * @param string $tipo     "cita" | "vacuna" | "cirugia"
     */
    public function __construct($resource, public string $tipo = "cita")
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        $mascota = $this->paciente;

        $comun = [
            "tipo" => $this->tipo,
            "tipo_label" => self::LABELES[$this->tipo] ?? $this->tipo,
            "color" => self::COLORES[$this->tipo] ?? "#878a99",
            "mascota" => [
                "name" => $mascota?->name ?? "—",
                "species" => $mascota?->species?->name ?? "—",
                "breed" => $mascota?->breed?->name ?? "—",
                "owner" => $mascota?->owner
                    ? trim(
                        $mascota->owner->first_name .
                            " " .
                            $mascota->owner->last_name,
                    )
                    : "—",
                "phone" => $mascota?->owner?->phone ?? "—",
            ],
            "veterinarian" => $this->veterinarian?->username
                ?? $this->user?->username
                ?? "—",
        ];

        return match ($this->tipo) {
            "vacuna" => $this->eventoVacuna($comun),
            "cirugia" => $this->eventoCirugia($comun),
            default => $this->eventoCita($comun),
        };
    }

    private function eventoCita(array $comun): array
    {
        $horaInicio = $this->appointment_time?->format("H:i");
        $duracion = (int) ($this->service?->duration_minutes ?? 30);
        $horaFin = $this->appointment_time?->copy()->addMinutes($duracion);

        return [
            "id" => "cita-" . $this->id,
            "title" => $comun["mascota"]["name"],
            "start" => $this->appointment_date?->format("Y-m-d") . "T" . $horaInicio,
            "end" => $this->appointment_date?->format("Y-m-d") . "T" . ($horaFin?->format("H:i") ?? $horaInicio),
            "allDay" => false,
            "extendedProps" => $comun + [
                "detalle" => $this->service?->name ?? "—",
                "status" => $this->status,
                "status_label" => $this->status ? ucfirst($this->status) : "—",
                "notas" => $this->medical_records?->first()?->notes ?? "—",
                "edit_url" => route("admin.citas.edit", $this->id),
            ],
        ];
    }

    private function eventoVacuna(array $comun): array
    {
        $fecha = $this->vaccination_date?->format("Y-m-d");
        $hora = $this->vaccination_time?->format("H:i");

        return [
            "id" => "vacuna-" . $this->id,
            "title" => $comun["mascota"]["name"],
            "start" => $fecha . ($hora ? "T" . $hora : ""),
            "end" => null,
            "allDay" => !$hora,
            "extendedProps" => $comun + [
                "detalle" => $this->vaccine_type?->name ?? "—",
                "proxima_dosis" => $this->next_due_date?->format("Y-m-d") ?? "—",
                "status" => $this->invoice?->status,
                "status_label" => $this->invoice?->status
                    ? ucfirst($this->invoice->status)
                    : "—",
                "notas" => $this->medical_records?->first()?->notes ?? "—",
                "edit_url" => route("admin.vacunas.edit", $this->id),
            ],
        ];
    }

    private function eventoCirugia(array $comun): array
    {
        return [
            "id" => "cirugia-" . $this->id,
            "title" => $comun["mascota"]["name"],
            "start" => $this->surgery_date?->format("Y-m-d\TH:i"),
            "end" => null,
            "allDay" => false,
            "extendedProps" => $comun + [
                "detalle" => $this->surgery_type ?? "—",
                "status" => $this->status,
                "status_label" => $this->status ? ucfirst($this->status) : "—",
                "notas" => $this->medical_notes ?? "—",
                "edit_url" => route("admin.cirugias.edit", $this->id),
            ],
        ];
    }
}
