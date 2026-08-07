<?php

namespace App\Actions\Pacientes;

use App\Models\Paciente;
use App\Support\ImageUploader;
use Illuminate\Validation\ValidationException;

class DeletePacienteAction
{
    public function execute(Paciente $paciente): void
    {
        if ($paciente->citas()->count() > 0) {
            throw ValidationException::withMessages([
                "paciente" => "No se puede eliminar la mascota porque tiene citas registradas.",
            ]);
        }

        if (!empty($paciente->photo_public_id)) {
            ImageUploader::delete($paciente->photo_public_id);
        }

        $paciente->delete();
    }
}