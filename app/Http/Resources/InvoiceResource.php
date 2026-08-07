<?php

namespace App\Http\Resources;

use App\Models\Cita;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "invoiceable_type" => $this->invoiceable_type,
            "invoiceable_id" => $this->invoiceable_id,
            "invoiceable_label" => $this->invoiceableLabel(),
            "owner_id" => $this->owner_id,
            "owner_name" => $this->owner?->first_name . " " . $this->owner?->last_name,
            "total" => $this->total,
            "remaining_balance" => $this->remaining_balance,
            "status" => $this->status,
            "issued_at" => $this->issued_at?->format("Y-m-d"),
            "payments" => PaymentResource::collection($this->whenLoaded("payments")),
            "show_url" => route("admin.invoices.show", $this->id),
        ];
    }

    private function invoiceableLabel(): string
    {
        $label = match ($this->invoiceable_type) {
            "cita" => "Cita",
            "vacuna" => "Vacuna",
            "surgiere" => "Cirugía",
            default => ucfirst((string) $this->invoiceable_type),
        };

        $petName = match ($this->invoiceable_type) {
            "cita" => Cita::query()->with("paciente")->find($this->invoiceable_id)?->paciente?->name,
            "vacuna" => Vacuna::query()->with("paciente")->find($this->invoiceable_id)?->paciente?->name,
            "surgiere" => Surgiere::query()->with("paciente")->find($this->invoiceable_id)?->paciente?->name,
            default => null,
        };

        return $petName
            ? "{$label} #{$this->invoiceable_id} — {$petName}"
            : "{$label} #{$this->invoiceable_id}";
    }
}