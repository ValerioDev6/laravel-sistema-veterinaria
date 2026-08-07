<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
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