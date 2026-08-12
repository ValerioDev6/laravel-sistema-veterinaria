<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(): View
    {
        return view("admin.reminders.index", [
            "title" => "Recordatorios",
            "pacientes" => $this->pacientes(),
            "tipos" => [
                "cita" => "Cita",
                "vacuna" => "Vacuna",
                "cirugia" => "Cirugía",
                "surgiere" => "Cirugía",
            ],
            "estados" => [
                "pendiente" => "Pendiente",
                "enviado" => "Enviado",
                "cancelado" => "Cancelado",
            ],
        ]);
    }

    private function pacientes(): array
    {
        return Paciente::orderBy("name")
            ->get()
            ->mapWithKeys(
                fn($p) => [
                    $p->id =>
                        $p->name .
                        " (" .
                        $p->owner?->first_name .
                        " " .
                        $p->owner?->last_name .
                        ")",
                ],
            )
            ->toArray();
    }
}