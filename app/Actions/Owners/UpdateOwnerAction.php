<?php

namespace App\Actions\Owners;

use App\Models\Owner;

class UpdateOwnerAction
{
    public function execute(Owner $owner, array $data): Owner
    {
        $owner->update($data);

        return $owner->fresh();
    }
}