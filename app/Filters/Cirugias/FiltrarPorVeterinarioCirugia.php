<?php

namespace App\Filters\Cirugias;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorVeterinarioCirugia
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("veterinarian_id")) {
            $query->where(
                "veterinarian_id",
                $this->request->integer("veterinarian_id"),
            );
        }

        return $next($query);
    }
}
