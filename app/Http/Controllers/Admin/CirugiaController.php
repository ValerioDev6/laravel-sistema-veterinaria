<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Paciente;
use App\Models\Species;
use App\Models\Surgiere;
use App\Models\User;
use Illuminate\View\View;

class CirugiaController extends Controller
{
    public function index(): View
    {
        return view("admin.cirugias.index", [
            "title" => "Cirugías",
            "veterinarians" => $this->veterinarians(),
            "species" => $this->species(),
            "paymentStatuses" => $this->paymentStatuses(),
        ]);
    }

    public function create(): View
    {
        return view("admin.cirugias.create", [
            "title" => "Registrar Cirugía",
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "veterinarians" => $this->veterinarians(),
            "citas" => $this->citas(),
        ]);
    }

    public function edit(Surgiere $cirugia): View
    {
        $invoice = Invoice::where("invoiceable_type", "surgiere")
            ->where("invoiceable_id", $cirugia->id)
            ->with("payments")
            ->first();

        $reminder = \App\Models\Reminder::where("pet_id", $cirugia->pet_id)
            ->where("remindable_type", "cirugia")
            ->where("remindable_id", $cirugia->id)
            ->first();

        return view("admin.cirugias.edit", [
            "title" => "Editar Cirugía",
            "cirugia" => $cirugia,
            "invoice" => $invoice,
            "reminder" => $reminder,
            "pacientes" => $this->pacientes(),
            "pacientesData" => $this->pacientesData(),
            "veterinarians" => $this->veterinarians(),
            "citas" => $this->citas(),
        ]);
    }

    private function pacientes(): array
    {
        return Paciente::orderBy("name")->pluck("name", "id")->toArray();
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

    private function citas(): array
    {
        return Cita::with("paciente")
            ->whereIn("status", ["pendiente", "confirmada"])
            ->orderByDesc("appointment_date")
            ->get()
            ->mapWithKeys(
                fn($c) => [
                    $c->id =>
                        "#{$c->id} — " .
                        $c->paciente?->name .
                        " ({$c->appointment_date?->format("d/m/Y")})",
                ],
            )
            ->toArray();
    }
}
