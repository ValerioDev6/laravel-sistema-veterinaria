<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Paciente;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $consultas = Cita::where("status", "completada")->get();
        foreach ($consultas as $cita) {
            $ownerId = $this->ownerOf($cita->pet_id);
            if (! $ownerId) {
                continue;
            }
            Invoice::updateOrCreate(
                [
                    "invoiceable_type" => "cita",
                    "invoiceable_id" => $cita->id,
                ],
                [
                    "owner_id" => $ownerId,
                    "total" => 50.00,
                    "remaining_balance" => 50.00,
                    "status" => "pendiente",
                    "issued_at" => $cita->appointment_date,
                ],
            );
        }

        $vacunas = Vacuna::all();
        foreach ($vacunas as $vacuna) {
            $ownerId = $this->ownerOf($vacuna->pet_id);
            if (! $ownerId) {
                continue;
            }
            Invoice::updateOrCreate(
                [
                    "invoiceable_type" => "vacuna",
                    "invoiceable_id" => $vacuna->id,
                ],
                [
                    "owner_id" => $ownerId,
                    "total" => 35.00,
                    "remaining_balance" => 35.00,
                    "status" => "pendiente",
                    "issued_at" => $vacuna->vaccination_date,
                ],
            );
        }

        $cirugias = Surgiere::where("status", "completada")->get();
        foreach ($cirugias as $cirugia) {
            $ownerId = $this->ownerOf($cirugia->pet_id);
            if (! $ownerId) {
                continue;
            }
            Invoice::updateOrCreate(
                [
                    "invoiceable_type" => "surgiere",
                    "invoiceable_id" => $cirugia->id,
                ],
                [
                    "owner_id" => $ownerId,
                    "total" => 150.00,
                    "remaining_balance" => 150.00,
                    "status" => "pendiente",
                    "issued_at" => $cirugia->surgery_date,
                ],
            );
        }
    }

    private function ownerOf(int $petId): ?int
    {
        $paciente = Paciente::with("owner")->find($petId);

        return $paciente?->owner?->id;
    }
}