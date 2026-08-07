<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function execute(array $data): User
    {
        $role = $data["role"] ?? null;
        $avatar = $data["avatar"] ?? null;
        unset($data["role"], $data["avatar"]);

        $data["password"] = Hash::make($data["password"]);

        if ($avatar) {
            $uploaded = ImageUploader::upload($avatar, "avatars");
            $data["avatar"] = $uploaded["url"];
            $data["avatar_public_id"] = $uploaded["public_id"];
        }

        $user = User::create($data);

        if ($role) {
            $user->assignRole($role);
        }

        return $user->fresh();
    }
}