<?php

namespace App\Actions\Owners;

use App\Models\Owner;

class CreateOwnerAction
{
    public function execute(array $data): Owner
    {
        return Owner::create($data);
    }
}