<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Cita;
use App\Models\Surgiere;
use App\Models\User;
use App\Models\Vacuna;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        return view("admin.usuarios.index", ["title" => "Personal"]);
    }

    public function create(): View
    {
        return view("admin.usuarios.create", [
            "title" => "Nuevo Usuario",
            "roles" => $this->roles(),
            "branches" => $this->branches(),
        ]);
    }

    public function edit(User $user): View
    {
        return view("admin.usuarios.edit", [
            "title" => "Editar Usuario",
            "user" => $user->load("branch"),
            "roles" => $this->roles(),
            "branches" => $this->branches(),
            "userRoles" => $user->getRoleNames(),
        ]);
    }

    public function movimientos(User $user): View
    {
        $citasVeterinario = Cita::where("veterinarian_id", $user->id)->count();
        $citasCreadas = Cita::where("created_by_user_id", $user->id)->count();
        $vacunas = Vacuna::where("veterinarian_id", $user->id)->count();
        $cirugias = Surgiere::where("veterinarian_id", $user->id)->count();

        return view("admin.usuarios.movimientos", [
            "title" => "Movimientos de " . $user->username,
            "user" => $user->load("branch"),
            "userRoles" => $user->getRoleNames(),
            "citasVeterinario" => $citasVeterinario,
            "citasCreadas" => $citasCreadas,
            "vacunas" => $vacunas,
            "cirugias" => $cirugias,
        ]);
    }

    private function roles(): array
    {
        return Role::where("guard_name", "api")
            ->orderBy("name")
            ->pluck("name", "name")
            ->toArray();
    }

    private function branches(): array
    {
        return Branch::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }
}
