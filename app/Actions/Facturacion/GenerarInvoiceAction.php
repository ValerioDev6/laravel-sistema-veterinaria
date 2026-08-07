<?php

namespace App\Actions\Facturacion;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class GenerarInvoiceAction
{
    public function execute(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $applyFirstPayment = filter_var($data["first_payment"] ?? false, FILTER_VALIDATE_BOOLEAN);
            $advance = $applyFirstPayment ? (float) $data["advance_amount"] : 0.0;
            $total = (float) $data["total"];

            $invoice = Invoice::create([
                "invoiceable_type" => $data["invoiceable_type"],
                "invoiceable_id" => $data["invoiceable_id"],
                "owner_id" => $data["owner_id"],
                "total" => $total,
                "remaining_balance" => round($total - $advance, 2),
                "status" => $applyFirstPayment
                    ? ($advance >= $total ? "pagado" : "parcial")
                    : "pendiente",
                "issued_at" => $data["issued_at"],
            ]);

            if ($applyFirstPayment && $advance > 0) {
                Payment::create([
                    "invoice_id" => $invoice->id,
                    "amount" => $advance,
                    "advance_amount" => $advance,
                    "payment_method" => $data["payment_method"],
                    "status" => "pagado",
                    "paid_at" => $data["issued_at"] . " " . now()->format("H:i:s"),
                ]);
            }

            return $invoice->fresh(["owner", "payments"]);
        });
    }
}