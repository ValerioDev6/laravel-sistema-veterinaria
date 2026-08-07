<?php

namespace App\Actions\Facturacion;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrarPagoAction
{
    public function execute(Invoice $invoice, array $data): Payment
    {
        if ($invoice->status === "anulado") {
            throw ValidationException::withMessages([
                "invoice" => ["No se pueden registrar pagos en una factura anulada."],
            ]);
        }

        $amount = (float) $data["amount"];

        if ($amount > $invoice->remaining_balance) {
            throw ValidationException::withMessages([
                "amount" => ["El monto excede el saldo pendiente de la factura."],
            ]);
        }

        return DB::transaction(function () use ($invoice, $data, $amount) {
            $payment = Payment::create([
                "invoice_id" => $invoice->id,
                "amount" => $amount,
                "advance_amount" => $data["advance_amount"] ?? 0,
                "payment_method" => $data["payment_method"],
                "status" => "pagado",
                "paid_at" => $data["paid_at"],
            ]);

            $this->recalcular($invoice);

            return $payment->fresh();
        });
    }

    private function recalcular(Invoice $invoice): void
    {
        $paid = (float) $invoice->payments()
            ->where("status", "pagado")
            ->sum("amount");

        $remaining = round($invoice->total - $paid, 2);
        $status = $remaining <= 0.005 ? "pagado" : ($paid > 0 ? "parcial" : "pendiente");

        $invoice->update([
            "remaining_balance" => max($remaining, 0.0),
            "status" => $status,
        ]);
    }
}