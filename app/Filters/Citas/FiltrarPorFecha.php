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
        if ($this->request->filled("appointment_date")) {
            $query->whereDate("appointment_date", $this->request->string("appointment_date"));
        }

        return $next($query);
    }
}