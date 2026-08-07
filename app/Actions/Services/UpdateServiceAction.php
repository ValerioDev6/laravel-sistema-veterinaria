<?php

namespace App\Actions\Services;

use App\Models\Service;

class UpdateServiceAction
{
    public static function execute(Service $service, array $data = []): Service
    {
        $service->update([
            "name" => data_get($data, "name"),
            "description" => data_get($data, "description"),
            "category" => data_get($data, "category"),
            "base_price" => data_get($data, "base_price"),
            "duration_minutes" => data_get($data, "duration_minutes"),
        ]);

        return $service->fresh();
    }
}