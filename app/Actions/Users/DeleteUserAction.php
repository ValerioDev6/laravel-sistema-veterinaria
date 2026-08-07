<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        $dependencias = collect([
            "citas" => $user->citas()->count(),
            "historiales" => $user->medical_records()->count(),
            "cirugías" => $user->surgieres()->count(),
            "vacunas" => $user->vacunas()->count(),
            "horarios" => $user->veterinarian_schedules()->count(),
        ])->filter(fn ($count) => $count > 0);

        if ($dependencias->isNotEmpty()) {
            throw ValidationException::withMessages([
                "usuario" => "No se puede eliminar el usuario porque tiene registros asociados: ".
                    $dependencias->keys()->implode(", ").".",
            ]);
        }

        $user->delete();
    }
}