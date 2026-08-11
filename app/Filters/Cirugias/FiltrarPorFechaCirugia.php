<?php

namespace App\Filters\Cirugias;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorFechaCirugia
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $desde = $this->request->input("surgery_date_from");
        $hasta = $this->request->input("surgery_date_to");

        if ($desde || $hasta) {
            $query->where(function ($q) use ($desde, $hasta) {
                if ($desde) {
                    $q->whereDate("surgery_date", ">=", $desde);
                }
                if ($hasta) {
                    $q->whereDate("surgery_date", "<=", $hasta);
                }
            });
        }

        return $next($query);
    }
}
