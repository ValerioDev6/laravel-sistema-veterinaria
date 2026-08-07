<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Vacuna;
use App\Models\VaccineType;
use Illuminate\View\View;

class VacunaController extends Controller
{
    public function index(): View
    {
        return view("admin.vacunas.index", ["title" => "Vacunas"]);
    }

    public function create(): View
    {
        return view("admin.vacunas.create", [
            "title" => "Registrar Vacuna",
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "vaccineTypes" => $this->vaccineTypes(),
            "citas" => $this->citas(),
        ]);
    }

    public function edit(Vacuna $vacuna): View
    {
        return view("admin.vacunas.edit", [
            "title" => "Editar Vacuna",
            "vacuna" => $vacuna,
            "pacientes" => $this->pacientes(),
            "veterinarians" => $this->veterinarians(),
            "vaccineTypes" => $this->vaccineTypes(),
            "citas" => $this->citas(),
        ]);
    }

    private function pacientes(): array
    {
        return Paciente::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }

    private function veterinarians(): array
    {
        return User::role("Veterinario")
            ->orderBy("username")
            ->pluck("username", "id")
            ->toArray();
    }

    private function vaccineTypes(): array
    {
        return VaccineType::with("species")
            ->orderBy("name")
            ->get()
            ->mapWithKeys(fn ($v) => [
                $v->id => $v->name . ($v->species ? " (" . $v->species->name . ")" : " (todas)"),
            ])
            ->toArray();
    }

    private function citas(): array
    {
        return Cita::with("paciente")
            ->whereIn("status", ["pendiente", "confirmada"])
            ->orderByDesc("appointment_date")
            ->get()
            ->mapWithKeys(fn ($c) => [
                $c->id => "#{$c->id} — " . $c->paciente?->name . " ({$c->appointment_date?->format('d/m/Y')})",
            ])
            ->toArray();
    }
}