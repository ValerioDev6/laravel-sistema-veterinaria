<?php

namespace App\Actions\Services;

use App\Models\Service;

class CreateServiceAction
{
    public static function execute(array $data = []): Service
    {
        return Service::create([
            "name" => data_get($data, "name"),
            "description" => data_get($data, "description"),
            "category" => data_get($data, "category"),
            "base_price" => data_get($data, "base_price"),
            "duration_minutes" => data_get($data, "duration_minutes"),
        ]);
    }
}