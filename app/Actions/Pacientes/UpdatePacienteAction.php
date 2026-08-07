<?php

namespace App\Actions\Pacientes;

use App\Models\Paciente;
use App\Support\ImageUploader;

class UpdatePacienteAction
{
    public function execute(Paciente $paciente, array $data): Paciente
    {
        if (!empty($data["photo"]) && $data["photo"]->isValid()) {
            $upload = ImageUploader::upload($data["photo"], "pacientes");

            if ($upload) {
                $data["photo"] = $upload["url"];
                $data["photo_public_id"] = $upload["public_id"] ?? null;
            } else {
                unset($data["photo"]);
            }
        } else {
            unset($data["photo"]);
        }

        $paciente->update($data);

        return $paciente->fresh();
    }
}