<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\View\View;

class SpeciesController extends Controller
{
    public function index(): View
    {
        return view("admin.species.index", ["title" => "Especies"]);
    }

    public function create(): View
    {
        return view("admin.species.create", ["title" => "Nueva Especie"]);
    }

    public function edit(Species $species): View
    {
        return view("admin.species.edit", [
            "title" => "Editar Especie",
            "species" => $species,
        ]);
    }
}