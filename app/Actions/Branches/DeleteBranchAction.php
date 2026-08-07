<?php

namespace App\Actions\Branches;

use App\Models\Branch;

class DeleteBranchAction
{
    public function execute(Branch $branch): void
    {
        $branch->delete();
    }
}