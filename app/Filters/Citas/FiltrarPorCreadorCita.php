<?php

namespace App\Filters\Citas;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorCreadorCita
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("created_by")) {
            $query->where("created_by_user_id", $this->request->integer("created_by"));
        }

        return $next($query);
    }
}