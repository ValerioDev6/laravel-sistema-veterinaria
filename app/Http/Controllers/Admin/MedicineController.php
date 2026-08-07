<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(): View
    {
        return view("admin.medicines.index", ["title" => "Medicamentos"]);
    }

    public function create(): View
    {
        return view("admin.medicines.create", ["title" => "Nuevo Medicamento"]);
    }

    public function edit(Medicine $medicine): View
    {
        return view("admin.medicines.edit", [
            "title" => "Editar Medicamento",
            "medicine" => $medicine,
        ]);
    }
}