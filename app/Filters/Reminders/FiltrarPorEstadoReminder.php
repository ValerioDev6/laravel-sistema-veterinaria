<?php

namespace App\Filters\Reminders;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorEstadoReminder
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("status")) {
            $query->where("status", $this->request->input("status"));
        }

        return $next($query);
    }
}