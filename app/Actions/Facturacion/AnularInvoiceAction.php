<?php

namespace App\Actions\Facturacion;

use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class AnularInvoiceAction
{
    public function execute(Invoice $invoice): Invoice
    {
        if ($invoice->status === "anulado") {
            return $invoice;
        }

        $hasPaid = $invoice->payments()
            ->where("status", "pagado")
            ->exists();

        if ($hasPaid) {
            throw ValidationException::withMessages([
                "invoice" => ["No se puede anular la factura porque ya tiene pagos registrados."],
            ]);
        }

        $invoice->update([
            "status" => "anulado",
        ]);

        return $invoice->fresh(["owner", "payments"]);
    }
}