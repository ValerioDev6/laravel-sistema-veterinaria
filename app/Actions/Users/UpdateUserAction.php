<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        $role = $data["role"] ?? null;
        $avatar = $data["avatar"] ?? null;
        unset($data["role"], $data["avatar"]);

        if (empty($data["password"])) {
            unset($data["password"]);
        } else {
            $data["password"] = Hash::make($data["password"]);
        }

        if ($avatar) {
            if ($user->avatar_public_id) {
                ImageUploader::delete($user->avatar_public_id);
            }
            $uploaded = ImageUploader::upload($avatar, "avatars");
            $data["avatar"] = $uploaded["url"];
            $data["avatar_public_id"] = $uploaded["public_id"];
        }

        $user->update($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user->fresh();
    }
}
