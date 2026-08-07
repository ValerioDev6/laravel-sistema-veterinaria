<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public static function execute(array $data = []): User
    {
        $datos = [
            "branch_id" => data_get($data, "branch_id"),
            "username" => data_get($data, "username"),
            "email" => data_get($data, "email"),
            "phone" => data_get($data, "phone"),
            "type_documento" => data_get($data, "type_documento"),
            "n_documento" => data_get($data, "n_documento"),
            "birthday" => data_get($data, "birthday"),
            "password" => Hash::make(data_get($data, "password")),
        ];

        $avatar = data_get($data, "avatar");
        if ($avatar) {
            $subida = ImageUploader::upload($avatar, "avatars");
            $datos["avatar"] = $subida["url"];
            $datos["avatar_public_id"] = $subida["public_id"];
        }

        $user = User::create($datos);

        $role = data_get($data, "role");
        if ($role) {
            $user->assignRole($role);
        }

        return $user->fresh();
    }
}