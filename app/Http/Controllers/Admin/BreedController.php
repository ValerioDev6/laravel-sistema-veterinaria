<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\View\View;

class BreedController extends Controller
{
    public function index(): View
    {
        return view("admin.breeds.index", ["title" => "Razas"]);
    }

    public function create(): View
    {
        return view("admin.breeds.create", [
            "title" => "Nueva Raza",
            "species" => $this->species(),
        ]);
    }

    public function edit(Breed $breed): View
    {
        return view("admin.breeds.edit", [
            "title" => "Editar Raza",
            "breed" => $breed,
            "species" => $this->species(),
        ]);
    }

    private function species(): array
    {
        return Species::orderBy("name")->pluck("name", "id")->toArray();
    }
}
