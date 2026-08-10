<?php

namespace App\Filters\Citas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorBusquedaCitas
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
                })->orWhereHas("veterinarian", function ($sub) use ($search) {
                    $sub->where("username", "like", "%{$search}%");
                })->orWhereHas("paciente.species", function ($sub) use ($search) {
                    $sub->where("name", "like", "%{$search}%");
                });
            });
        }

        return $next($query);
    }
}
