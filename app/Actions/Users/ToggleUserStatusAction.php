<?php

namespace App\Actions\Users;

use App\Models\User;

class ToggleUserStatusAction
{
    public function execute(User $user): User
    {
        $user->update(["is_active" => ! $user->is_active]);

        return $user->fresh();
    }
}