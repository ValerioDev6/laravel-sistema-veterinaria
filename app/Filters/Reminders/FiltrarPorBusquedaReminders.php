<?php

namespace App\Filters\Reminders;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorBusquedaReminders
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $search = trim((string) $this->request->input("search", ""));

        if ($search !== "") {
            $query->where(function ($q) use ($search) {
                $q->where("message", "like", "%{$search}%")
                    ->orWhere("remindable_type", "like", "%{$search}%")
                    ->orWhereHas("paciente", function ($sub) use ($search) {
                        $sub->where("name", "like", "%{$search}%");
                    });
            });
        }

        return $next($query);
    }
}