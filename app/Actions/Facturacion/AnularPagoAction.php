<?php

namespace App\Actions\Facturacion;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AnularPagoAction
{
    public function execute(Payment $payment): Payment
    {
        if ($payment->status === "anulado") {
            return $payment;
        }

        return DB::transaction(function () use ($payment) {
            $payment->update(["status" => "anulado"]);

            $this->recalcular($payment->invoice);

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