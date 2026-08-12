<?php

namespace App\Actions\Citas;

use App\Actions\Reminders\SincronizarReminderAction;
use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class UpdateCitaAction
{
    public static function execute(Cita $cita, array $data = []): Cita
    {
        return DB::transaction(function () use ($cita, $data) {
            ValidarDisponibilidadCita::execute($data, $cita->id);

            $serviceId = data_get($data, "service_id");

            if (! $serviceId && ! empty($data["new_service"])) {
                $service = Service::create([
                    "name" => $data["new_service"]["name"],
                    "category" => $data["new_service"]["category"] ?? "otro",
                    "base_price" => $data["new_service"]["base_price"] ?? 0,
                    "duration_minutes" =>
                        $data["new_service"]["duration_minutes"] ?? 30,
                    "description" =>
                        $data["new_service"]["description"] ?? null,
                ]);
                $serviceId = $service->id;
            }

            $cita->update([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "service_id" => $serviceId,
                "appointment_date" => data_get($data, "appointment_date"),
                "appointment_time" => data_get($data, "appointment_time"),
                "reason" => data_get($data, "reason"),
                "reprogramming" => data_get($data, "reprogramming", false),
                "status" => data_get($data, "status", $cita->status),
            ]);

            $cita->load("service", "paciente");
            self::registrarPago($cita, $data);

            SincronizarReminderAction::execute(
                "cita",
                $cita->pet_id,
                $cita->id,
                $cita->appointment_date,
                $cita->reason ? "Cita: " . $cita->reason : "Cita programada",
            );

            return $cita->fresh(["paciente", "veterinarian", "service"]);
        });
    }

    private static function registrarPago(Cita $cita, array $data): void
    {
        $total = (float) ($cita->service?->base_price ?? 0);
        $advance = (float) data_get($data, "advance_amount", 0);
        $method = data_get($data, "payment_method", "otro");

        $invoice = Invoice::where("invoiceable_type", "cita")
            ->where("invoiceable_id", $cita->id)
            ->first();

        if ($invoice) {
            $invoice->update([
                "owner_id" => $cita->paciente?->owner_id,
                "total" => $total,
                "remaining_balance" => round($total - $advance, 2),
                "status" => $advance >= $total ? "pagado" : "parcial",
                "issued_at" => $cita->appointment_date
                    ? $cita->appointment_date->format("Y-m-d")
                    : now()->toDateString(),
            ]);
        } else {
            $invoice = Invoice::create([
                "invoiceable_type" => "cita",
                "invoiceable_id" => $cita->id,
                "owner_id" => $cita->paciente?->owner_id,
                "total" => $total,
                "remaining_balance" => round($total - $advance, 2),
                "status" => $advance >= $total ? "pagado" : "parcial",
                "issued_at" => $cita->appointment_date
                    ? $cita->appointment_date->format("Y-m-d")
                    : now()->toDateString(),
            ]);
        }

        if ($advance > 0) {
            $payment = Payment::where("invoice_id", $invoice->id)->first();

            if ($payment) {
                $payment->update([
                    "amount" => $advance,
                    "advance_amount" => $advance,
                    "payment_method" => $method,
                    "status" => "pagado",
                    "paid_at" => now(),
                ]);
            } else {
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

        if ($advance >= $total && $cita->status === "pendiente") {
            $cita->update(["status" => "confirmada"]);
        }
    }
}
