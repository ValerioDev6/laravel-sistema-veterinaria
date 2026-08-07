<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        return view("admin.branches.index", ["title" => "Sucursales"]);
    }

    public function create(): View
    {
        return view("admin.branches.create", ["title" => "Nueva Sucursal"]);
    }

    public function edit(Branch $branch): View
    {
        return view("admin.branches.edit", [
            "title" => "Editar Sucursal",
            "branch" => $branch,
        ]);
    }
}