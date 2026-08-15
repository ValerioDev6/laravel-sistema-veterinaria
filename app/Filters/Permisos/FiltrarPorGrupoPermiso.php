<?php

namespace App\Filters\Permisos;

use App\Support\PermissionCatalog;
use Closure;
use Illuminate\Http\Request;

class FiltrarPorGrupoPermiso
{
    public function __construct(protected Request $request)
    {
    }

    public function handle($query, Closure $next)
    {
        $grupo = trim((string) $this->request->input("grupo", ""));

        if ($grupo === "") {
            return $next($query);
        }

        if ($grupo === "Otros") {
            $query->whereNotIn("name", PermissionCatalog::allKnown());
        } else {
            $nombres = PermissionCatalog::groups()[$grupo] ?? [];

            if ($nombres === []) {
                $query->whereRaw("1 = 0");
            } else {
                $query->whereIn("name", $nombres);
            }
        }

        return $next($query);
    }
}
