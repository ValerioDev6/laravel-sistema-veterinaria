<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Facturacion\AnularInvoiceAction;
use App\Actions\Facturacion\GenerarInvoiceAction;
use App\Actions\Facturacion\ListInvoicesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoices\StoreInvoiceRequest;
use App\Http\Requests\Invoices\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(
        Request $request,
        ListInvoicesAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => InvoiceResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" => $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function store(
        StoreInvoiceRequest $request,
        GenerarInvoiceAction $action
    ): JsonResponse {
        $invoice = $action->execute($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Factura generada correctamente",
            "data" => new InvoiceResource($invoice->load(["owner", "payments"])),
            "errors" => (object) [],
        ], 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            "status" => true,
            "message" => "Detalle de la factura",
            "data" => new InvoiceResource(
                $invoice->load(["owner", "payments"])
            ),
            "errors" => (object) [],
        ]);
    }

    public function update(
        UpdateInvoiceRequest $request,
        Invoice $invoice
    ): JsonResponse {
        $invoice->update($request->validated());

        return response()->json([
            "status" => true,
            "message" => "Factura actualizada correctamente",
            "data" => new InvoiceResource($invoice->fresh(["owner", "payments"])),
            "errors" => (object) [],
        ]);
    }

    public function anular(
        Invoice $invoice,
        AnularInvoiceAction $action
    ): JsonResponse {
        try {
            $invoice = $action->execute($invoice);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->errors()["invoice"][0] ?? "No se pudo anular la factura.",
                "data" => (object) [],
                "errors" => (object) $e->errors(),
            ], 422);
        }

        return response()->json([
            "status" => true,
            "message" => "Factura anulada correctamente",
            "data" => new InvoiceResource($invoice),
            "errors" => (object) [],
        ]);
    }
}