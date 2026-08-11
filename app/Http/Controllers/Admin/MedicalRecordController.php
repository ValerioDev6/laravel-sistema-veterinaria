<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Medicine;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(): View
    {
        $pacientes = Paciente::orderBy("name")->get();

        return view("admin.medical-records.index", [
            "title" => "Historial Médico",
            "pacientes" => $pacientes,
        ]);
    }

    public function create(): View
    {
        $pacientes = Paciente::orderBy("name")->get();
        $veterinarios = User::role("Veterinario")->orderBy("username")->get();
        $medicines = Medicine::orderBy("name")->get();
        $citas = Cita::with("paciente")->orderByDesc("appointment_date")->limit(50)->get();

        return view("admin.medical-records.create", [
            "title" => "Nueva Entrada de Historial",
            "pacientes" => $pacientes,
            "pacientesData" => $this->pacientesData(),
            "veterinarios" => $veterinarios,
            "medicines" => $medicines,
            "citas" => $citas,
        ]);
    }

    public function show(int $id): View
    {
        $record = \App\Models\MedicalRecord::with([
            "paciente",
            "user",
            "cita",
            "prescriptions.medicine",
            "vital_signs",
            "medical_record_attachments",
        ])->findOrFail($id);

        return view("admin.medical-records.show", [
            "title" => "Detalle del Historial",
            "record" => $record,
        ]);
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
}