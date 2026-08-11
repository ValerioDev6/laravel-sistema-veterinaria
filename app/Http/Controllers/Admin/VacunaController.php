<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Paciente;
use App\Models\Species;
use App\Models\User;
use App\Models\Vacuna;
use App\Models\VaccineType;
use Illuminate\View\View;

class VacunaController extends Controller
{
    public function index(): View
    {
        return view("admin.vacunas.index", [
            "title" => "Vacunas",
            "veterinarians" => $this->veterinarians(),
            "species" => $this->species(),
            "paymentStatuses" => $this->paymentStatuses(),
        ]);
    }

    public function create(): View
    {
        return view("admin.vacunas.create", [
            "title" => "Registrar Vacuna",
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "veterinarians" => $this->veterinarians(),
            "vaccineTypes" => $this->vaccineTypes(),
            "species" => $this->species(),
        ]);
    }

    public function edit(Vacuna $vacuna): View
    {
        $invoice = Invoice::where("invoiceable_type", "vacuna")
            ->where("invoiceable_id", $vacuna->id)
            ->with("payments")
            ->first();

        return view("admin.vacunas.edit", [
            "title" => "Editar Vacuna",
            "vacuna" => $vacuna,
            "invoice" => $invoice,
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "veterinarians" => $this->veterinarians(),
            "vaccineTypes" => $this->vaccineTypes(),
            "species" => $this->species(),
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

    private function paymentStatuses(): array
    {
        return [
            "pendiente" => "Pendiente",
            "parcial" => "Parcial",
            "pagado" => "Pagado",
            "anulado" => "Anulado",
        ];
    }

    private function vaccineTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return VaccineType::with("species")
            ->orderBy("name")
            ->get(["id", "name", "base_price", "species_id"]);
    }
}