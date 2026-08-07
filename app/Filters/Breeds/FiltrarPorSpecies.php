<?php

namespace App\Filters\Breeds;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorSpecies
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("species_id")) {
            $query->where("species_id", $this->request->integer("species_id"));
        }

        return $next($query);
    }
}