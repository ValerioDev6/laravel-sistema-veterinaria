<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Validation\ValidationException;

class DeleteServiceAction
{
    public function execute(Service $service): void
    {
        if ($service->citas()->count() > 0) {
            throw ValidationException::withMessages([
                "service" => "No se puede eliminar el servicio porque tiene citas asociadas.",
            ]);
        }

        $service->delete();
    }
}