<?php

namespace App\Actions\Branches;

use App\Models\Branch;

class CreateBranchAction
{
    public static function execute(array $data = []): Branch
    {
        return Branch::create([
            "name" => data_get($data, "name"),
            "address" => data_get($data, "address"),
            "city" => data_get($data, "city"),
            "phone" => data_get($data, "phone"),
        ]);
    }
}