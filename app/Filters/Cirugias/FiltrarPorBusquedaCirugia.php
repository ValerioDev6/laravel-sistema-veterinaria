<?php

namespace App\Filters\Cirugias;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorBusquedaCirugia
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $search = trim((string) $this->request->input("search", ""));

        if ($search !== "") {
            $query->where(function ($q) use ($search) {
                $q->whereHas("paciente", function ($sub) use ($search) {
                    $sub->where("name", "like", "%{$search}%");
                })->orWhereHas("user", function ($sub) use ($search) {
                    $sub->where("username", "like", "%{$search}%");
                })->orWhere("surgery_type", "like", "%{$search}%");
            });
        }

        return $next($query);
    }
}
