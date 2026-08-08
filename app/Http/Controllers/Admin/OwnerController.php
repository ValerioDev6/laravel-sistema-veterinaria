<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\View\View;

class OwnerController extends Controller
{
    public function index(): View
    {
        return view("admin.owners.index", ["title" => "Propietarios"]);
    }

    public function show(Owner $owner): View
    {
        $owner->load("pacientes.breed.species");

        return view("admin.owners.show", [
            "title" => "Ficha del Propietario",
            "owner" => $owner,
        ]);
    }
}