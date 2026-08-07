<?php

namespace App\Filters\Shared;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorBusqueda
{
    /**
     * @param  array<int, string>  $columns
     */
    public function __construct(
        protected Request $request,
        protected array $columns,
    ) {
    }

    public function handle($query, Closure $next)
    {
        $search = trim((string) $this->request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                foreach ($this->columns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $next($query);
    }
}
