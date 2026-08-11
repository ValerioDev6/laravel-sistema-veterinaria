<?php

namespace App\Actions\Citas;

use App\Models\Cita;

class ObtenerCitasCalendarioAction
{
    /**
     * Todas las citas con las relaciones necesarias para el calendario,
     * sin paginación (a diferencia del index del DataTable).
     */
    public function execute(): \Illuminate\Database\Eloquent\Collection
    {
        return Cita::with([
            "paciente.species",
            "paciente.breed",
            "paciente.owner",
            "veterinarian",
            "service",
            "medical_records" => fn ($q) => $q->orderByDesc("created_at"),
        ])->get();
    }
}
