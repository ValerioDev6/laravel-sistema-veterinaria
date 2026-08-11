<?php

namespace App\Actions\Calendario;

use App\Models\Cita;
use App\Models\Surgiere;
use App\Models\Vacuna;

class CalendarioAction
{
    /**
     * Citas, vacunas y cirugías con las relaciones necesarias para el
     * calendario unificado, sin paginación.
     */
    public function execute(): array
    {
        $citas = Cita::with([
            "paciente.species",
            "paciente.breed",
            "paciente.owner",
            "veterinarian",
            "service",
            "medical_records" => fn ($q) => $q->orderByDesc("created_at"),
        ])->get();

        $vacunas = Vacuna::with([
            "paciente.species",
            "paciente.breed",
            "paciente.owner",
            "user",
            "vaccine_type",
            "invoice",
            "medical_records" => fn ($q) => $q->orderByDesc("created_at"),
        ])->get();

        $cirugias = Surgiere::with([
            "paciente.species",
            "paciente.breed",
            "paciente.owner",
            "user",
            "medical_records" => fn ($q) => $q->orderByDesc("created_at"),
        ])->get();

        return [
            "citas" => $citas,
            "vacunas" => $vacunas,
            "cirugias" => $cirugias,
        ];
    }
}
