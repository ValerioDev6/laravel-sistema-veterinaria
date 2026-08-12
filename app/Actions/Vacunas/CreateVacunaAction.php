<?php

namespace App\Actions\Vacunas;

use App\Actions\Reminders\SincronizarReminderAction;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Payment;
use App\Models\Vacuna;
use App\Models\VaccineType;
use Illuminate\Support\Facades\DB;

class CreateVacunaAction
{
    public static function execute(array $data = []): Vacuna
    {
        return DB::transaction(function () use ($data) {
            ValidarDisponibilidadVacuna::execute($data);

            $vaccineTypeId = data_get($data, "vaccine_type_id");

            if (! $vaccineTypeId && ! empty($data["new_vaccine_type"])) {
                $vaccineType = VaccineType::create([
                    "name" => $data["new_vaccine_type"]["name"],
                    "species_id" => $data["new_vaccine_type"]["species_id"] ?? null,
                    "base_price" => $data["new_vaccine_type"]["base_price"] ?? 0,
                ]);
                $vaccineTypeId = $vaccineType->id;
            }

            $vacuna = Vacuna::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "vaccine_type_id" => $vaccineTypeId,
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_date" => data_get($data, "vaccination_date"),
                "vaccination_time" => data_get($data, "vaccination_time"),
                "next_due_date" => data_get($data, "next_due_date"),
            ]);

            MedicalRecord::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_id" => $vacuna->id,
                "event_type" => "vacuna",
                "event_date" => data_get($data, "vaccination_date"),
                "notes" => "Vacunación: " . ($vacuna->vaccine_type?->name ?? ""),
            ]);

            SincronizarReminderAction::execute(
                "vacuna",
                $vacuna->pet_id,
                $vacuna->id,
                $vacuna->next_due_date,
                "Próxima dosis de " . ($vacuna->vaccine_type?->name ?? "vacuna"),
            );

            self::registrarPago($vacuna, $data);

            return $vacuna->load(["vaccine_type", "paciente", "user", "invoice"]);
        });
    }

    private static function registrarPago(Vacuna $vacuna, array $data): void
    {
        $total = (float) ($vacuna->vaccine_type?->base_price ?? 0);
        $advance = (float) data_get($data, "advance_amount", 0);
        $method = data_get($data, "payment_method", "otro");

        $invoice = Invoice::create([
            "invoiceable_type" => "vacuna",
            "invoiceable_id" => $vacuna->id,
            "owner_id" => $vacuna->paciente?->owner_id,
            "total" => $total,
            "remaining_balance" => round($total - $advance, 2),
            "status" => $advance >= $total ? "pagado" : ($advance > 0 ? "parcial" : "pendiente"),
            "issued_at" => $vacuna->vaccination_date
                ? $vacuna->vaccination_date->format("Y-m-d")
                : now()->toDateString(),
        ]);

        if ($advance > 0) {
            Payment::create([
                "invoice_id" => $invoice->id,
                "amount" => $advance,
                "advance_amount" => $advance,
                "payment_method" => $method,
                "status" => "pagado",
                "paid_at" => now(),
            ]);
        }
    }
}