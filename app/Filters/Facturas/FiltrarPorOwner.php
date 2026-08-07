<?php

namespace App\Filters\Facturas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorOwner
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("owner_id")) {
            $query->where("owner_id", $this->request->integer("owner_id"));
        }

        return $next($query);
    }
}