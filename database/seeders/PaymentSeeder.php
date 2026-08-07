<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = Invoice::orderBy("id")->get();

        foreach ($invoices as $index => $invoice) {
            $mode = $index % 4; 

            if ($mode === 0) {
                continue;
            }

            if ($mode === 1) {
                $payment = Payment::updateOrCreate(
                    [
                        "invoice_id" => $invoice->id,
                        "amount" => $invoice->total,
                    ],
                    [
                        "payment_method" => "efectivo",
                        "status" => "pagado",
                        "paid_at" => $invoice->issued_at,
                    ],
                );
                $invoice->update([
                    "remaining_balance" => 0.0,
                    "status" => "pagado",
                ]);
                continue;
            }

            $half = round($invoice->total / 2, 2);
            Payment::updateOrCreate(
                [
                    "invoice_id" => $invoice->id,
                    "amount" => $half,
                ],
                [
                    "advance_amount" => $half,
                    "payment_method" => "tarjeta",
                    "status" => "pagado",
                    "paid_at" => $invoice->issued_at,
                ],
            );
            $invoice->update([
                "remaining_balance" => round($invoice->total - $half, 2),
                "status" => "parcial",
            ]);
        }
    }
}