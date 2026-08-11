<?php

namespace App\Filters\Vacunas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorEspecieVacuna
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("species_id")) {
            $query->whereHas("paciente", function ($q) {
                $q->where("species_id", $this->request->integer("species_id"));
            });
        }

        return $next($query);
    }
}