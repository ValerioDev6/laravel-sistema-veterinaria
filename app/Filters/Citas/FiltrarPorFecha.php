<?php

namespace App\Filters\Citas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorFecha
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $desde = $this->request->input("appointment_date_from");
        $hasta = $this->request->input("appointment_date_to");

        if ($desde || $hasta) {
            $query->where(function ($q) use ($desde, $hasta) {
                if ($desde) {
                    $q->whereDate("appointment_date", ">=", $desde);
                }
                if ($hasta) {
                    $q->whereDate("appointment_date", "<=", $hasta);
                }
            });
        } elseif ($this->request->filled("appointment_date")) {
            $query->whereDate(
                "appointment_date",
                $this->request->string("appointment_date"),
            );
        }

        return $next($query);
    }
}
