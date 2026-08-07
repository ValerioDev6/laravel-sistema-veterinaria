<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Surgiere;
use App\Models\User;
use Illuminate\View\View;

class CirugiaController extends Controller
{
    public function index(): View
    {
        return view("admin.cirugias.index", ["title" => "Cirugías"]);
    }

    public function create(): View
    {
        return view("admin.cirugias.create", [
            "title" => "Registrar Cirugía",
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "citas" => $this->citas(),
        ]);
    }

    public function edit(Surgiere $cirugia): View
    {
        return view("admin.cirugias.edit", [
            "title" => "Editar Cirugía",
            "cirugia" => $cirugia,
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "citas" => $this->citas(),
        ]);
    }

    private function pacientes(): array
    {
        return Paciente::orderBy("name")->pluck("name", "id")->toArray();
    }

    private function veterinarians(): array
    {
        return User::role("Veterinario")
            ->orderBy("username")
            ->pluck("username", "id")
            ->toArray();
    }

    private function citas(): array
    {
        return Cita::with("paciente")
            ->whereIn("status", ["pendiente", "confirmada"])
            ->orderByDesc("appointment_date")
            ->get()
            ->mapWithKeys(
                fn($c) => [
                    $c->id =>
                        "#{$c->id} — " .
                        $c->paciente?->name .
                        " ({$c->appointment_date?->format("d/m/Y")})",
                ],
            )
            ->toArray();
    }
}
