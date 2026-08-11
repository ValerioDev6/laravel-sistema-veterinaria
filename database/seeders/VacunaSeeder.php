<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\User;
use App\Models\Vacuna;
use App\Models\VaccineType;
use Illuminate\Database\Seeder;

class VacunaSeeder extends Seeder
{
    public function run(): void
    {
        $vacunas = [
            ["pet" => "Rocky", "vet" => "dr.torres", "type" => "Séxtuple canina", "date" => "2026-05-15", "time" => "09:30", "next" => "2027-05-15"],
            ["pet" => "Luna", "vet" => "dr.ramos", "type" => "Triple felina", "date" => "2026-06-10", "time" => "10:00", "next" => "2027-06-10"],
            ["pet" => "Max", "vet" => "dr.paredes", "type" => "Rabia canina", "date" => "2026-04-20", "time" => "08:30", "next" => null],
            ["pet" => "Misha", "vet" => "dr.torres", "type" => "Rabia felina", "date" => "2026-03-05", "time" => "15:30", "next" => null],
            ["pet" => "Simba", "vet" => "dr.ramos", "type" => "Leucemia felina", "date" => "2026-07-22", "time" => "11:00", "next" => "2027-07-22"],
            ["pet" => "Kiara", "vet" => "dr.paredes", "type" => "Séxtuple canina", "date" => "2026-06-26", "time" => "09:00", "next" => "2027-06-26"],
        ];

        foreach ($vacunas as $vacuna) {
            $pet = Paciente::where("name", $vacuna["pet"])->first();
            $vet = User::where("username", $vacuna["vet"])->first();
            $type = VaccineType::where("name", $vacuna["type"])->first();

            if (! $pet || ! $vet || ! $type) {
                continue;
            }

            Vacuna::updateOrCreate(
                [
                    "pet_id" => $pet->id,
                    "vaccine_type_id" => $type->id,
                    "vaccination_date" => $vacuna["date"],
                ],
                [
                    "veterinarian_id" => $vet->id,
                    "vaccination_time" => $vacuna["time"],
                    "next_due_date" => $vacuna["next"],
                ],
            );
        }
    }
}