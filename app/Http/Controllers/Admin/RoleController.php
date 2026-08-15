<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PermissionCatalog;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        return view("admin.roles.index", ["title" => "Roles y Permisos"]);
    }

    public function create(): View
    {
        return view("admin.roles.create", [
            "title" => "Nuevo Rol",
            "grupos" => $this->gruposConPermisos(),
        ]);
    }

    public function edit(Role $role): View
    {
        return view("admin.roles.edit", [
            "title" => "Editar Rol",
            "role" => $role,
            "grupos" => $this->gruposConPermisos(),
            "permisosActuales" => $role->permissions()->pluck("id")->all(),
        ]);
    }

    /**
     * Permisos del guard api agrupados por módulo (catálogo + desconocidos en "Otros").
     *
     * @return array<string, array<int, array{id: int, name: string, label: string}>>
     */
    protected function gruposConPermisos(): array
    {
        $permisos = Permission::where("guard_name", "api")
            ->orderBy("name")
            ->get()
            ->keyBy("name");

        $grupos = [];

        foreach (PermissionCatalog::groups() as $grupo => $nombres) {
            $items = [];

            foreach ($nombres as $nombre) {
                if ($permisos->has($nombre)) {
                    $items[] = [
                        "id" => $permisos[$nombre]->id,
                        "name" => $nombre,
                        "label" => PermissionCatalog::labelFor($nombre),
                    ];
                }
            }

            if ($items !== []) {
                $grupos[$grupo] = $items;
            }
        }

        $otros = $permisos
            ->reject(fn ($p) => in_array($p->name, PermissionCatalog::allKnown(), true))
            ->values();

        if ($otros->isNotEmpty()) {
            $grupos["Otros"] = $otros
                ->map(fn ($p) => [
                    "id" => $p->id,
                    "name" => $p->name,
                    "label" => PermissionCatalog::labelFor($p->name),
                ])
                ->all();
        }

        return $grupos;
    }
}
