<?php

namespace App\Filters\Vacunas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorFechaVacuna
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $desde = $this->request->input("vaccination_date_from");
        $hasta = $this->request->input("vaccination_date_to");

        if ($desde || $hasta) {
            $query->where(function ($q) use ($desde, $hasta) {
                if ($desde) {
                    $q->whereDate("vaccination_date", ">=", $desde);
                }
                if ($hasta) {
                    $q->whereDate("vaccination_date", "<=", $hasta);
                }
            });
        } elseif ($this->request->filled("vaccination_date")) {
            $query->whereDate(
                "vaccination_date",
                $this->request->string("vaccination_date"),
            );
        }

        return $next($query);
    }
}