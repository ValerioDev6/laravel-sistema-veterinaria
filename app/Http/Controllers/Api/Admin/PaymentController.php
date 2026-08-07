<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Facturacion\AnularPagoAction;
use App\Actions\Facturacion\ListPaymentsAction;
use App\Actions\Facturacion\RegistrarPagoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(
        Request $request,
        ListPaymentsAction $action
    ): JsonResponse {
        $paginated = $action->execute($request);

        return response()->json([
            "success" => true,
            "data" => PaymentResource::collection($paginated->items()),
            "pagination" => [
                "total" => $paginated->total(),
                "per_page" => $paginated->perPage(),
                "current_page" => $paginated->currentPage(),
                "last_page" => $paginated->lastPage(),
                "has_more" =>
                    $paginated->currentPage() < $paginated->lastPage(),
            ],
        ]);
    }

    public function store(
        StorePaymentRequest $request,
        Invoice $invoice,
        RegistrarPagoAction $action,
    ): JsonResponse {
        try {
            $payment = $action->execute($invoice, $request->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(
                [
                    "status" => false,
                    "message" =>
                        $e->errors()["amount"][0] ??
                        ($e->errors()["invoice"][0] ??
                            "No se pudo registrar el pago."),
                    "data" => (object) [],
                    "errors" => (object) $e->errors(),
                ],
                422,
            );
        }

        return response()->json(
            [
                "status" => true,
                "message" => "Pago registrado correctamente",
                "data" => new PaymentResource($payment),
                "errors" => (object) [],
            ],
            201,
        );
    }

    public function anular(
        Payment $payment,
        AnularPagoAction $action,
    ): JsonResponse {
        $payment = $action->execute($payment);

        return response()->json([
            "status" => true,
            "message" => "Pago anulado correctamente",
            "data" => new PaymentResource($payment),
            "errors" => (object) [],
        ]);
    }
}
