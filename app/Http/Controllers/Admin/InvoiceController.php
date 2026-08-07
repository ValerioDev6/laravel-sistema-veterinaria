<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Owner;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view("admin.facturacion.invoices.index", [
            "title" => "Facturas",
            "owners" => Owner::orderBy("first_name")->get(),
        ]);
    }

    public function create(): View
    {
        return view("admin.facturacion.invoices.create", [
            "title" => "Nueva Factura",
            "owners" => Owner::orderBy("first_name")->get(),
            "citas" => $this->citas(),
            "vacunas" => $this->vacunas(),
            "cirugias" => $this->cirugias(),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(["owner", "payments"]);

        return view("admin.facturacion.invoices.show", [
            "title" => "Factura #{$invoice->id}",
            "invoice" => $invoice,
        ]);
    }

    private function citas(): array
    {
        return Cita::with("paciente")
            ->where("status", "completada")
            ->orderByDesc("appointment_date")
            ->get()
            ->mapWithKeys(fn ($c) => [
                $c->id => "#{$c->id} — {$c->paciente?->name} ({$c->appointment_date?->format('d/m/Y')})",
            ])
            ->toArray();
    }

    private function vacunas(): array
    {
        return Vacuna::with("paciente")
            ->orderByDesc("vaccination_date")
            ->get()
            ->mapWithKeys(fn ($v) => [
                $v->id => "#{$v->id} — {$v->paciente?->name} ({$v->vaccination_date?->format('d/m/Y')})",
            ])
            ->toArray();
    }

    private function cirugias(): array
    {
        return Surgiere::with("paciente")
            ->where("status", "completada")
            ->orderByDesc("surgery_date")
            ->get()
            ->mapWithKeys(fn ($s) => [
                $s->id => "#{$s->id} — {$s->paciente?->name} ({$s->surgery_date?->format('d/m/Y')})",
            ])
            ->toArray();
    }
}