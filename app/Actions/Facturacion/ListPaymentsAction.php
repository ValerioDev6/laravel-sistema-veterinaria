<?php

namespace App\Actions\Facturacion;

use App\Filters\Pagos\FiltrarPorEstado as FiltrarPorEstadoPago;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListPaymentsAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Payment::with('invoice.owner'))
            ->through([
                new FiltrarPorEstadoPago($request),
                new FiltrarPorBusqueda($request, ['status', 'payment_method']),
                new OrdenarPor($request, [0 => 'id', 1 => 'status', 2 => 'amount', 3 => 'paid_at'], [3, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}