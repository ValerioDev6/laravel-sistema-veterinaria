<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view("admin.services.index", ["title" => "Servicios"]);
    }

    public function create(): View
    {
        return view("admin.services.create", [
            "title" => "Nuevo Servicio",
            "categories" => $this->categories(),
        ]);
    }

    public function edit(Service $service): View
    {
        return view("admin.services.edit", [
            "title" => "Editar Servicio",
            "service" => $service,
            "categories" => $this->categories(),
        ]);
    }

    private function categories(): array
    {
        return [
            "consulta" => "Consulta",
            "vacunacion" => "Vacunación",
            "cirugia" => "Cirugía",
            "estetica" => "Estética",
            "otro" => "Otro",
        ];
    }
}