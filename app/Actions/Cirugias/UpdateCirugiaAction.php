<?php

namespace App\Actions\Cirugias;

use App\Actions\Reminders\SincronizarReminderAction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Surgiere;
use Illuminate\Support\Facades\DB;

class UpdateCirugiaAction
{
    public static function execute(Surgiere $cirugia, array $data = []): Surgiere
    {
        return DB::transaction(function () use ($cirugia, $data) {
            $surgeryDate = self::armarFechaHora($data);

            $cirugia->update([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "surgery_type" => data_get($data, "surgery_type"),
                "surgery_date" => $surgeryDate,
                "outcome" => data_get($data, "outcome"),
                "status" => data_get($data, "status"),
                "medical_notes" => data_get($data, "medical_notes"),
            ]);

            $cirugia->load(["paciente"]);
            self::registrarPago($cirugia, $data, $surgeryDate);

            SincronizarReminderAction::execute(
                "cirugia",
                $cirugia->pet_id,
                $cirugia->id,
                $cirugia->surgery_date,
                $cirugia->surgery_type
                    ? "Cirugía: " . $cirugia->surgery_type
                    : "Cirugía programada",
            );

            return $cirugia->fresh([
                "paciente",
                "user",
                "invoice",
                "invoice.payments",
            ]);
        });
    }

    private static function armarFechaHora(array $data): ?string
    {
        $fecha = data_get($data, "surgery_date");
        $hora = data_get($data, "surgery_time");

        if (! $fecha) {
            return null;
        }

        if ($hora) {
            $fecha = date("Y-m-d", strtotime($fecha)) . " " . $hora;
        }

        return $fecha;
    }

    private static function registrarPago(Surgiere $cirugia, array $data, ?string $surgeryDate = null): void
    {
        $total = (float) data_get($data, "total", 0);
        $advance = (float) data_get($data, "advance_amount", 0);
        $method = data_get($data, "payment_method", "otro");

        $invoice = Invoice::where("invoiceable_type", "surgiere")
            ->where("invoiceable_id", $cirugia->id)
            ->first();

        $payload = [
            "owner_id" => $cirugia->paciente?->owner_id,
            "total" => $total,
            "remaining_balance" => round($total - $advance, 2),
            "status" => $advance >= $total ? "pagado" : ($advance > 0 ? "parcial" : "pendiente"),
            "issued_at" => $surgeryDate
                ? date("Y-m-d", strtotime($surgeryDate))
                : (($cirugia->surgery_date?->format("Y-m-d")) ?? now()->toDateString()),
        ];

        if ($invoice) {
            $invoice->update($payload);
        } else {
            $invoice = Invoice::create(
                array_merge(
                    ["invoiceable_type" => "surgiere", "invoiceable_id" => $cirugia->id],
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
