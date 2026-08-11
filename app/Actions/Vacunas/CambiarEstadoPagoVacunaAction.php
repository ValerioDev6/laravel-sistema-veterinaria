<?php

namespace App\Actions\Vacunas;

use App\Actions\Facturacion\AnularPagoAction;
use App\Actions\Facturacion\RegistrarPagoAction;
use App\Models\Invoice;
use App\Models\Vacuna;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CambiarEstadoPagoVacunaAction
{
    public function execute(Vacuna $vacuna, string $status): Vacuna
    {
        return DB::transaction(function () use ($vacuna, $status) {
            $invoice = Invoice::where("invoiceable_type", "vacuna")
                ->where("invoiceable_id", $vacuna->id)
                ->first();

            if (! $invoice) {
                throw ValidationException::withMessages([
                    "status" => ["La vacuna no tiene factura asociada."],
                ]);
            }

            $this->aplicar($invoice, $status);

            return $vacuna->fresh(["invoice", "invoice.payments"]);
        });
    }

    private function aplicar(Invoice $invoice, string $status): void
    {
        if ($status === "pagado") {
            $restante = (float) $invoice->remaining_balance;
            if ($restante > 0.005) {
                (new RegistrarPagoAction())->execute($invoice, [
                    "amount" => $restante,
                    "advance_amount" => $restante,
                    "payment_method" => "efectivo",
                    "paid_at" => now(),
                ]);
            } else {
                $invoice->update([
                    "remaining_balance" => 0.0,
                    "status" => "pagado",
                ]);
            }
            return;
        }

        if ($status === "pendiente") {
            foreach ($invoice->payments()->where("status", "pagado")->get() as $payment) {
                (new AnularPagoAction())->execute($payment);
            }
            $invoice->refresh();
            if ($invoice->status !== "pendiente") {
                $invoice->update([
                    "remaining_balance" => $invoice->total,
                    "status" => "pendiente",
                ]);
            }
            return;
        }

        if ($status === "anulado") {
            $invoice->update([
                "remaining_balance" => $invoice->total,
                "status" => "anulado",
            ]);
            return;
        }
    }
}
