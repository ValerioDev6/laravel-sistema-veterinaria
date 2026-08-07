<?php

namespace App\Actions\Facturacion;

use App\Filters\Facturas\FiltrarPorEstado;
use App\Filters\Facturas\FiltrarPorOwner;
use App\Filters\Facturas\FiltrarPorRangoFecha;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListInvoicesAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(Invoice::with(['owner', 'payments']))
            ->through([
                new FiltrarPorEstado($request),
                new FiltrarPorRangoFecha($request),
                new FiltrarPorOwner($request),
                new FiltrarPorBusqueda($request, ['status', 'total']),
                new OrdenarPor($request, [0 => 'id', 1 => 'status', 2 => 'total', 3 => 'issued_at'], [3, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}