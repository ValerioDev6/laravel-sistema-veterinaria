<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Paciente;
use App\Models\Prescription;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = Paciente::pluck("id")->toArray();
        $veterinarios = User::role("Veterinario")->pluck("id")->toArray();
        $medicines = Medicine::pluck("id")->toArray();
        $citas = Cita::pluck("id")->toArray();

        if (empty($pacientes) || empty($veterinarios)) {
            $this->command->warn("No hay pacientes o veterinarios para sembrar historial médico.");

            return;
        }

        $tipos = ["consulta", "vacuna", "cirugia", "otro"];

        foreach (range(1, 8) as $i) {
            $pacienteId = $pacientes[array_rand($pacientes)];
            $tipo = $tipos[array_rand($tipos)];

            $data = [
                "pet_id" => $pacienteId,
                "veterinarian_id" => $veterinarios[array_rand($veterinarios)],
                "event_type" => $tipo,
                "event_date" => now()->subDays(rand(1, 120)),
                "notes" => $this->notasAleatorias(),
            ];

            if (!empty($citas) && $tipo === "consulta") {
                $data["cita_id"] = $citas[array_rand($citas)];
            }

            $record = MedicalRecord::updateOrCreate(
                ["id" => $i],
                $data,
            );

            if (rand(0, 1)) {
                VitalSign::updateOrCreate(
                    ["medical_record_id" => $record->id],
                    [
                        "pet_id" => $pacienteId,
                        "weight" => rand(5, 400) / 10,
                        "temperature" => rand(370, 392) / 10,
                        "heart_rate" => rand(60, 160),
                        "recorded_at" => $record->event_date,
                    ],
                );
            }

            if (!empty($medicines) && $tipo !== "otro" && rand(0, 1)) {
                Prescription::updateOrCreate(
                    ["id" => $i * 10],
                    [
                        "medical_record_id" => $record->id,
                        "medicine_id" => $medicines[array_rand($medicines)],
                        "dosage" => rand(1, 3) . " veces al día",
                        "duration_days" => rand(3, 10),
                    ],
                );
            }
        }
    }

    private function notasAleatorias(): string
    {
        $notas = [
            "Paciente presenta buen estado general. Se recomienda control en 15 días.",
            "Se observa leve mejoría con el tratamiento indicado.",
            "Requiere seguimiento por posible alergia alimentaria.",
            "Control de rutina sin hallazgos relevantes.",
            "Se programó examen complementario para descartar infección.",
        ];

        return $notas[array_rand($notas)];
    }
}