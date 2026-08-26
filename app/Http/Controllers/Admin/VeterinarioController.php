<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\View\View;

class VeterinarioController extends Controller
{
    public function index(): View
    {
        return view("admin.veterinarios.index", ["title" => "Veterinarios"]);
    }

    public function create(): View
    {
        return view("admin.veterinarios.create", [
            "title" => "Nuevo Veterinario",
            "branches" => $this->branches(),
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->hasRole("Veterinario"), 404);

        $horarios = $user
            ->veterinarian_schedules()
            ->orderBy("day_of_week")
            ->orderBy("start_time")
            ->get()
            ->map(
                fn($h) => [
                    "day_of_week" => (int) $h->day_of_week,
                    "start_time" => $h->start_time->format("H:i"),
                    "end_time" => $h->end_time->format("H:i"),
                ],
            )
            ->values();

        return view("admin.veterinarios.edit", [
            "title" => "Editar Veterinario",
            "user" => $user->load("branch"),
            "branches" => $this->branches(),
            "userRoles" => $user->getRoleNames(),
            "horarios" => $horarios,
        ]);
    }

    private function branches(): array
    {
        return Branch::orderBy("name")
            ->pluck("name", "id")
            ->toArray();
    }
}
