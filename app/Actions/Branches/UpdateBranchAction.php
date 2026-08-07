<?php

namespace App\Actions\Branches;

use App\Models\Branch;

class UpdateBranchAction
{
    public function execute(Branch $branch, array $data): Branch
    {
        $branch->update($data);

        return $branch->fresh();
    }
}