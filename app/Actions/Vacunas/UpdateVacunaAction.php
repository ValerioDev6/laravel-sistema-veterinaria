<?php

namespace App\Actions\Vacunas;

use App\Actions\Reminders\SincronizarReminderAction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Vacuna;
use App\Models\VaccineType;
use Illuminate\Support\Facades\DB;

class UpdateVacunaAction
{
    public static function execute(Vacuna $vacuna, array $data = []): Vacuna
    {
        return DB::transaction(function () use ($vacuna, $data) {
            ValidarDisponibilidadVacuna::execute($data, $vacuna->id);

            $vaccineTypeId = data_get($data, "vaccine_type_id");

            if (! $vaccineTypeId && ! empty($data["new_vaccine_type"])) {
                $vaccineType = VaccineType::create([
                    "name" => $data["new_vaccine_type"]["name"],
                    "species_id" => $data["new_vaccine_type"]["species_id"] ?? null,
                    "base_price" => $data["new_vaccine_type"]["base_price"] ?? 0,
                ]);
                $vaccineTypeId = $vaccineType->id;
            }

            $vacuna->update([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "vaccine_type_id" => $vaccineTypeId,
                "cita_id" => data_get($data, "cita_id"),
                "vaccination_date" => data_get($data, "vaccination_date"),
                "vaccination_time" => data_get($data, "vaccination_time"),
                "next_due_date" => data_get($data, "next_due_date"),
            ]);

            $vacuna->load(["vaccine_type", "paciente"]);
            self::registrarPago($vacuna, $data);

            SincronizarReminderAction::execute(
                "vacuna",
                $vacuna->pet_id,
                $vacuna->id,
                $vacuna->next_due_date,
                "Próxima dosis de " . ($vacuna->vaccine_type?->name ?? "vacuna"),
            );

            return $vacuna->fresh(["vaccine_type", "paciente", "user", "invoice"]);
        });
    }

    private static function registrarPago(Vacuna $vacuna, array $data): void
    {
        $total = (float) ($vacuna->vaccine_type?->base_price ?? 0);
        $advance = (float) data_get($data, "advance_amount", 0);
        $method = data_get($data, "payment_method", "otro");

        $invoice = Invoice::where("invoiceable_type", "vacuna")
            ->where("invoiceable_id", $vacuna->id)
            ->first();

        $payload = [
            "owner_id" => $vacuna->paciente?->owner_id,
            "total" => $total,
            "remaining_balance" => round($total - $advance, 2),
            "status" => $advance >= $total ? "pagado" : ($advance > 0 ? "parcial" : "pendiente"),
            "issued_at" => $vacuna->vaccination_date?->format("Y-m-d") ?? now()->toDateString(),
        ];

        if ($invoice) {
            $invoice->update($payload);
        } else {
            $invoice = Invoice::create(
                array_merge(
                    ["invoiceable_type" => "vacuna", "invoiceable_id" => $vacuna->id],
                    $payload,
                ),
            );
        }

        if ($advance > 0) {
            $payment = Payment::where("invoice_id", $invoice->id)->first();

            $paymentPayload = [
                "amount" => $advance,
                "advance_amount" => $advance,
                "payment_method" => $method,
                "status" => "pagado",
                "paid_at" => now(),
            ];

            if ($payment) {
                $payment->update($paymentPayload);
            } else {
                Payment::create(
                    array_merge(["invoice_id" => $invoice->id], $paymentPayload),
                );
            }
        }
    }
}