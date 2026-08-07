<?php

namespace App\Actions\Pacientes;

use App\Models\Paciente;
use App\Support\ImageUploader;

class CreatePacienteAction
{
    public function execute(array $data): Paciente
    {
        $upload = null;
        if (!empty($data["photo"]) && $data["photo"]->isValid()) {
            $upload = ImageUploader::upload($data["photo"], "pacientes");
        }

        if ($upload) {
            $data["photo"] = $upload["url"];
            $data["photo_public_id"] = $upload["public_id"] ?? null;
        } else {
            unset($data["photo"]);
        }

        return Paciente::create($data);
    }
}