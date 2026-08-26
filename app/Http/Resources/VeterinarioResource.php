<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeterinarioResource extends UserResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            "edit_url" => route("admin.veterinarios.edit", $this->id),
            "horarios" => $this->whenLoaded(
                "veterinarian_schedules",
                fn () => $this->veterinarian_schedules
                    ->sortBy([["day_of_week", "asc"], ["start_time", "asc"]])
                    ->map(
                        fn ($h) => [
                            "day_of_week" => (int) $h->day_of_week,
                            "start_time" => $h->start_time->format("H:i"),
                            "end_time" => $h->end_time->format("H:i"),
                        ],
                    )
                    ->values(),
            ),
        ]);
    }
}
