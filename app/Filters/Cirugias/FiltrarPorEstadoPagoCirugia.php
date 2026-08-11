<?php

namespace App\Filters\Cirugias;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorEstadoPagoCirugia
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $estado = $this->request->input("payment_status");

        if ($estado && in_array($estado, ["pendiente", "parcial", "pagado", "anulado"], true)) {
            $query->whereHas("invoice", function ($q) use ($estado) {
                $q->where("status", $estado);
            });
        }

        return $next($query);
    }
}
