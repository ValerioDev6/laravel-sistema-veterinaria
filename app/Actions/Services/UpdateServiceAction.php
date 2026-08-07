<?php

namespace App\Actions\Services;

use App\Models\Service;

class UpdateServiceAction
{
    public function execute(Service $service, array $data): Service
    {
        $service->update($data);

        return $service->fresh();
    }
}