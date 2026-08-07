<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Surgiere;
use App\Models\User;
use Illuminate\Database\Seeder;

class CirugiaSeeder extends Seeder
{
    public function run(): void
    {
        $cirugias = [
            ["pet" => "Thor", "vet" => "dr.ramos", "type" => "Esterilización", "date" => "2026-07-01 10:00", "status" => "completada", "outcome" => "Recuperación satisfactoria"],
            ["pet" => "Rocky", "vet" => "dr.torres", "type" => "Limpieza dental", "date" => "2026-07-15 09:00", "status" => "completada", "outcome" => null],
            ["pet" => "Misha", "vet" => "dr.paredes", "type" => "Extracción de tumor", "date" => "2026-08-01 11:00", "status" => "en_proceso", "outcome" => null],
        ];

        foreach ($cirugias as $cirugia) {
            $pet = Paciente::where("name", $cirugia["pet"])->first();
            $vet = User::where("username", $cirugia["vet"])->first();

            if (! $pet || ! $vet) {
                continue;
            }

            Surgiere::updateOrCreate(
                [
                    "pet_id" => $pet->id,
                    "surgery_type" => $cirugia["type"],
                    "surgery_date" => $cirugia["date"],
                ],
                [
                    "veterinarian_id" => $vet->id,
                    "status" => $cirugia["status"],
                    "outcome" => $cirugia["outcome"],
                ],
            );
        }
    }
}