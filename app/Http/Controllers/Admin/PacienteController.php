<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Paciente;
use App\Models\Species;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(): View
    {
        return view("admin.pacientes.index", [
            "title" => "Pacientes",
            "owners" => $this->owners(),
            "species" => $this->species(),
        ]);
    }

    public function create(): View
    {
        return view("admin.pacientes.create", [
            "title" => "Nueva Mascota",
            "owners" => $this->owners(),
            "species" => $this->species(),
        ]);
    }

    public function edit(Paciente $paciente): View
    {
        return view("admin.pacientes.edit", [
            "title" => "Editar Mascota",
            "paciente" => $paciente,
            "owners" => $this->owners(),
            "species" => $this->species(),
        ]);
    }

    public function show(Paciente $paciente): View
    {
        $paciente->load(["owner", "species", "breed"]);

        return view("admin.pacientes.show", [
            "title" => "Ficha del Paciente",
            "paciente" => $paciente,
        ]);
    }

    private function owners(): array
    {
        return Owner::orderBy("last_name")
            ->orderBy("first_name")
            ->get()
            ->mapWithKeys(fn ($owner) => [
                $owner->id => $owner->first_name . " " . $owner->last_name,
            ])
            ->toArray();
    }

    private function species(): array
    {
        return Species::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }
}