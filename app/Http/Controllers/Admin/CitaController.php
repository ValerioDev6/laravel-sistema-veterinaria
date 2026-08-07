<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(): View
    {
        return view("admin.citas.index", [
            "title" => "Citas",
            "veterinarians" => $this->veterinarians(),
            "statuses" => $this->statuses(),
        ]);
    }

    public function create(): View
    {
        return view("admin.citas.create", [
            "title" => "Nueva Cita",
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "services" => $this->services(),
        ]);
    }

    public function edit(Cita $cita): View
    {
        return view("admin.citas.edit", [
            "title" => "Editar Cita",
            "cita" => $cita,
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "services" => $this->services(),
        ]);
    }

    public function calendar(): View
    {
        return view("admin.citas.calendar", [
            "title" => "Calendario de Citas",
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

    private function veterinarians(): array
    {
        return User::role("Veterinario")
            ->orderBy("username")
            ->pluck("username", "id")
            ->toArray();
    }

    private function services(): array
    {
        return Service::orderBy("name")->pluck("name", "id")->toArray();
    }

    private function statuses(): array
    {
        return [
            "pendiente" => "Pendiente",
            "confirmada" => "Confirmada",
            "completada" => "Completada",
            "cancelada" => "Cancelada",
        ];
    }
}
