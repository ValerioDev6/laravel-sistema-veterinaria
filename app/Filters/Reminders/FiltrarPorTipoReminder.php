<?php

namespace App\Filters\Reminders;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorTipoReminder
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("tipo")) {
            $query->where("remindable_type", $this->request->input("tipo"));
        }

        return $next($query);
    }
}