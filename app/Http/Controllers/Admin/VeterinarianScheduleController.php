<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VeterinarianSchedule;
use Illuminate\View\View;

class VeterinarianScheduleController extends Controller
{
    public function index(): View
    {
        return view("admin.veterinarian-schedules.index", [
            "title" => "Horarios de Veterinarios",
        ]);
    }

    public function create(): View
    {
        return view("admin.veterinarian-schedules.create", [
            "title" => "Nuevo Horario",
            "veterinarians" => $this->veterinarians(),
            "days" => $this->days(),
        ]);
    }

    public function edit(VeterinarianSchedule $veterinarianSchedule): View
    {
        return view("admin.veterinarian-schedules.edit", [
            "title" => "Editar Horario",
            "schedule" => $veterinarianSchedule,
            "veterinarians" => $this->veterinarians(),
            "days" => $this->days(),
        ]);
    }

    private function veterinarians(): array
    {
        return User::role("Veterinario")
            ->orderBy("username")
            ->pluck("username", "id")
            ->toArray();
    }

    private function days(): array
    {
        return [
            1 => "Lunes",
            2 => "Martes",
            3 => "Miércoles",
            4 => "Jueves",
            5 => "Viernes",
            6 => "Sábado",
            0 => "Domingo",
        ];
    }
}