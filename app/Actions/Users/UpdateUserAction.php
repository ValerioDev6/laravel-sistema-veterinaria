<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public static function execute(User $user, array $data = []): User
    {
        $datos = [
            "branch_id" => data_get($data, "branch_id"),
            "username" => data_get($data, "username"),
            "email" => data_get($data, "email"),
            "phone" => data_get($data, "phone"),
            "type_documento" => data_get($data, "type_documento"),
            "n_documento" => data_get($data, "n_documento"),
            "birthday" => data_get($data, "birthday"),
        ];

        $password = data_get($data, "password");
        if (!empty($password)) {
            $datos["password"] = Hash::make($password);
        }

        $avatar = data_get($data, "avatar");
        if ($avatar) {
            if ($user->avatar_public_id) {
                ImageUploader::delete($user->avatar_public_id);
            }
            $subida = ImageUploader::upload($avatar, "avatars");
            $datos["avatar"] = $subida["url"];
            $datos["avatar_public_id"] = $subida["public_id"];
        }

        $user->update($datos);

        $role = data_get($data, "role");
        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user->fresh();
    }
}