<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Service;
use App\Models\Species;
use App\Models\User;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(): View
    {
        return view("admin.citas.index", [
            "title" => "Citas",
            "veterinarians" => $this->veterinarians(),
            "species" => $this->species(),
            "statuses" => $this->statuses(),
        ]);
    }

    public function create(): View
    {
        return view("admin.citas.create", [
            "title" => "Nueva Cita",
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "services" => $this->serviciosConPrecio(),
            "categories" => $this->categories(),
        ]);
    }

    public function edit(Cita $cita): View
    {
        $invoice = \App\Models\Invoice::where("invoiceable_type", "cita")
            ->where("invoiceable_id", $cita->id)
            ->with("payments")
            ->first();

        $reminder = \App\Models\Reminder::where("pet_id", $cita->pet_id)
            ->where("remindable_type", "cita")
            ->where("remindable_id", $cita->id)
            ->first();

        return view("admin.citas.edit", [
            "title" => "Editar Cita",
            "cita" => $cita,
            "invoice" => $invoice,
            "reminder" => $reminder,
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "services" => $this->serviciosConPrecio(),
            "categories" => $this->categories(),
        ]);
    }

    public function calendar(): View
    {
        return view("admin.citas.calendar", [
            "title" => "Calendario de Citas",
        ]);
    }

    private function pacientes(): array
    {
        return Paciente::orderBy("name")
            ->get()
            ->mapWithKeys(
                fn($p) => [
                    $p->id =>
                        $p->name .
                        " (" .
                        $p->owner?->first_name .
                        " " .
                        $p->owner?->last_name .
                        ")",
                ],
            )
            ->toArray();
    }

    private function pacientesData(): array
    {
        return Paciente::with(["species", "breed", "owner"])
            ->orderBy("name")
            ->get()
            ->mapWithKeys(
                fn($p) => [
                    $p->id => [
                        "id" => $p->id,
                        "name" => $p->name,
                        "photo" => $p->photo,
                        "species" => $p->species?->name,
                        "breed" => $p->breed?->name,
                        "gender" => $p->gender,
                        "weight" => $p->weight,
                        "owner" =>
                            $p->owner?->first_name .
                            " " .
                            $p->owner?->last_name,
                    ],
                ],
            )
            ->toArray();
    }

    private function veterinarians(): array
    {
        return User::role("Veterinario")
            ->orderBy("username")
            ->pluck("username", "id")
            ->toArray();
    }

    private function species(): array
    {
        return Species::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }

    private function serviciosConPrecio(): \Illuminate\Database\Eloquent\Collection
    {
        return Service::orderBy("name")
            ->get(["id", "name", "base_price", "duration_minutes", "category"]);
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

    private function statuses(): array
    {
        return [
            "pendiente" => "Pendiente",
            "confirmada" => "Confirmada",
            "completada" => "Completada",
            "cancelada" => "Cancelada",
        ];
    }
}
