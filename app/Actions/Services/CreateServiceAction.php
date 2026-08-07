<?php

namespace App\Actions\Services;

use App\Models\Service;

class CreateServiceAction
{
    public function execute(array $data): Service
    {
        return Service::create($data);
    }
}