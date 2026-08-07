<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Species;
use App\Models\VaccineType;
use Illuminate\View\View;

class VaccineTypeController extends Controller
{
    public function index(): View
    {
        return view("admin.vaccine-types.index", ["title" => "Tipos de Vacuna"]);
    }

    public function create(): View
    {
        return view("admin.vaccine-types.create", [
            "title" => "Nuevo Tipo de Vacuna",
            "species" => $this->species(),
        ]);
    }

    public function edit(VaccineType $vaccineType): View
    {
        return view("admin.vaccine-types.edit", [
            "title" => "Editar Tipo de Vacuna",
            "vaccineType" => $vaccineType,
            "species" => $this->species(),
        ]);
    }

    private function species(): array
    {
        return Species::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }
}