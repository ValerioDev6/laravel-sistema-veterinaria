<?php

namespace App\Filters\Facturas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorEstado
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("status")) {
            $query->where("status", $this->request->string("status"));
        }

        return $next($query);
    }
}