<?php

namespace App\Filters\Reminders;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorPetReminder
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        if ($this->request->filled("pet_id")) {
            $query->where("pet_id", $this->request->integer("pet_id"));
        }

        return $next($query);
    }
}