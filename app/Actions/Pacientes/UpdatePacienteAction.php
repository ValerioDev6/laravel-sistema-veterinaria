<?php

namespace App\Actions\Pacientes;

use App\Models\Owner;
use App\Models\Paciente;
use App\Support\ImageUploader;

class UpdatePacienteAction
{
    public static function execute(Paciente $paciente, array $data = []): Paciente
    {
        $ownerId = data_get($data, "owner_id", $paciente->owner_id);

        if ($ownerId) {
            Owner::whereKey($ownerId)->update([
                "first_name" => data_get($data, "first_name"),
                "last_name" => data_get($data, "last_name"),
                "email" => data_get($data, "email"),
                "phone" => data_get($data, "phone"),
                "address" => data_get($data, "address"),
                "city" => data_get($data, "city"),
                "type_documento" => data_get($data, "type_documento"),
                "n_documento" => data_get($data, "n_documento"),
            ]);
        }

        $datos = [
            "owner_id" => $ownerId,
            "species_id" => data_get($data, "species_id"),
            "breed_id" => data_get($data, "breed_id"),
            "name" => data_get($data, "name"),
            "birth_date" => data_get($data, "birth_date"),
            "gender" => data_get($data, "gender"),
            "color" => data_get($data, "color"),
            "weight" => data_get($data, "weight"),
            "medical_notes" => data_get($data, "medical_notes"),
        ];

        $foto = data_get($data, "photo");
        if (!empty($foto) && $foto->isValid()) {
            $subida = ImageUploader::upload($foto, "pacientes");
            if ($subida) {
                $datos["photo"] = $subida["url"];
                $datos["photo_public_id"] = $subida["public_id"] ?? null;
            }
        }

        $paciente->update($datos);

        return $paciente->fresh();
    }
}