<?php

namespace App\Filters\Facturas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorRangoFecha
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("desde")) {
            $query->whereDate("issued_at", ">=", $this->request->date("desde"));
        }

        if ($this->request->filled("hasta")) {
            $query->whereDate("issued_at", "<=", $this->request->date("hasta"));
        }

        return $next($query);
    }
}