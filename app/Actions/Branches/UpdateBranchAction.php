<?php

namespace App\Actions\Branches;

use App\Models\Branch;

class UpdateBranchAction
{
    public static function execute(Branch $branch, array $data = []): Branch
    {
        $branch->update([
            "name" => data_get($data, "name"),
            "address" => data_get($data, "address"),
            "city" => data_get($data, "city"),
            "phone" => data_get($data, "phone"),
        ]);

        return $branch->fresh();
    }
}