<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $vets = User::role("Veterinario")->get();

        if ($vets->isEmpty()) {
            return;
        }

        $consultas = Service::where("category", "consulta")->pluck("id")->toArray();
        $vacunacion = Service::where("category", "vacunacion")->first()?->id;

        $citas = [
            ["pet" => "Rocky", "vet" => "dr.torres", "date" => "2026-08-06", "time" => "09:30", "status" => "confirmada", "reason" => "Chequeo general"],
            ["pet" => "Luna", "vet" => "dr.ramos", "date" => "2026-08-06", "time" => "10:00", "status" => "pendiente", "reason" => "Vacuna de refuerzo"],
            ["pet" => "Max", "vet" => "dr.paredes", "date" => "2026-08-07", "time" => "11:00", "status" => "confirmada", "reason" => "Control de peso"],
            ["pet" => "Misha", "vet" => "dr.torres", "date" => "2026-08-07", "time" => "15:30", "status" => "pendiente", "reason" => "Dolor abdominal"],
            ["pet" => "Thor", "vet" => "dr.ramos", "date" => "2026-08-08", "time" => "09:00", "status" => "confirmada", "reason" => "Revisión post cirugía"],
            ["pet" => "Pelusa", "vet" => "dr.paredes", "date" => "2026-08-08", "time" => "16:00", "status" => "pendiente", "reason" => "Corte de uñas"],
            ["pet" => "Simba", "vet" => "dr.torres", "date" => "2026-08-10", "time" => "10:30", "status" => "pendiente", "reason" => "Vacunación anual"],
            ["pet" => "Bruno", "vet" => "dr.ramos", "date" => "2026-08-11", "time" => "12:00", "status" => "pendiente", "reason" => "Control dermatológico"],
        ];

        foreach ($citas as $cita) {
            $pet = Paciente::where("name", $cita["pet"])->first();
            $vet = User::where("username", $cita["vet"])->first();

            if (! $pet || ! $vet) {
                continue;
            }

            $serviceId = $cita["reason"] === "Vacuna de refuerzo" || $cita["reason"] === "Vacunación anual"
                ? $vacunacion
                : ($consultas[0] ?? null);

            Cita::updateOrCreate(
                [
                    "pet_id" => $pet->id,
                    "veterinarian_id" => $vet->id,
                    "appointment_date" => $cita["date"],
                    "appointment_time" => $cita["time"],
                ],
                [
                    "service_id" => $serviceId,
                    "created_by_user_id" => $vet->id,
                    "reason" => $cita["reason"],
                    "status" => $cita["status"],
                ],
            );
        }
    }
}