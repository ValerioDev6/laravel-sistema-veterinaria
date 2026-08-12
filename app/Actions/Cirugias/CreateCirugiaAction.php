<?php

namespace App\Actions\Cirugias;

use App\Actions\Reminders\SincronizarReminderAction;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Payment;
use App\Models\Surgiere;
use Illuminate\Support\Facades\DB;

class CreateCirugiaAction
{
    public static function execute(array $data = []): Surgiere
    {
        return DB::transaction(function () use ($data) {
            $surgeryDate = self::armarFechaHora($data);

            $cirugia = Surgiere::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "surgery_type" => data_get($data, "surgery_type"),
                "surgery_date" => $surgeryDate,
                "outcome" => data_get($data, "outcome"),
                "status" => data_get($data, "status"),
                "medical_notes" => data_get($data, "medical_notes"),
            ]);

            MedicalRecord::create([
                "pet_id" => data_get($data, "pet_id"),
                "veterinarian_id" => data_get($data, "veterinarian_id"),
                "cita_id" => data_get($data, "cita_id"),
                "surgery_id" => $cirugia->id,
                "event_type" => "cirugia",
                "event_date" => $surgeryDate,
                "notes" => "Cirugía: " . (data_get($data, "surgery_type") ?? "sin tipo"),
            ]);

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

            return $cirugia->load([
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

        $invoice = Invoice::create([
            "invoiceable_type" => "surgiere",
            "invoiceable_id" => $cirugia->id,
            "owner_id" => $cirugia->paciente?->owner_id,
            "total" => $total,
            "remaining_balance" => round($total - $advance, 2),
            "status" => $advance >= $total ? "pagado" : ($advance > 0 ? "parcial" : "pendiente"),
            "issued_at" => $surgeryDate
                ? date("Y-m-d", strtotime($surgeryDate))
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
