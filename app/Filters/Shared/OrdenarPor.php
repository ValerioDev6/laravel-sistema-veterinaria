<?php

namespace App\Filters\Shared;

use Closure;
use Illuminate\Http\Request;

class OrdenarPor
{
    /**
     * @param  array<int, string>  $orderable  mapa de índice de columna JS => columna SQL
     * @param  array{0: int, 1: string}  $default  [índice de columna, dirección]
     */
    public function __construct(
        protected Request $request,
        protected array $orderable,
        protected array $default,
    ) {
    }

    public function handle($query, Closure $next)
    {
        $column =
            $this->orderable[
                (int) $this->request->input('sort_by', $this->default[0])
            ] ?? $this->orderable[$this->default[0]];

        $dir =
            strtolower((string) $this->request->input('sort_dir', $this->default[1])) ===
            'desc'
                ? 'desc'
                : 'asc';

        return $next($query->orderBy($column, $dir));
    }
}