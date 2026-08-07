<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "invoice_id" => $this->invoice_id,
            "invoice_label" => "Factura #{$this->invoice_id}",
            "owner_name" => $this->whenLoaded(
                "invoice",
                fn () => $this->invoice?->owner
                    ? "{$this->invoice->owner->first_name} {$this->invoice->owner->last_name}"
                    : "-"
            ),
            "invoice_url" => $this->whenLoaded("invoice", fn () => route("admin.invoices.show", $this->invoice_id)),
            "amount" => $this->amount,
            "advance_amount" => $this->advance_amount,
            "payment_method" => $this->payment_method,
            "status" => $this->status,
            "paid_at" => $this->paid_at?->format("Y-m-d H:i"),
        ];
    }
}