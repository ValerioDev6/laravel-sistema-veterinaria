<?php

namespace Database\Seeders;

use App\Models\Species;
use App\Models\VaccineType;
use Illuminate\Database\Seeder;

class VaccineTypeSeeder extends Seeder
{
    public function run(): void
    {
        $dog = Species::where("name", "Perro")->first()?->id;
        $cat = Species::where("name", "Gato")->first()?->id;

        $vaccineTypes = [
            ["name" => "Séxtuple canina", "species_id" => $dog],
            ["name" => "Rabia canina", "species_id" => $dog],
            ["name" => "Triple felina", "species_id" => $cat],
            ["name" => "Rabia felina", "species_id" => $cat],
            ["name" => "Leucemia felina", "species_id" => $cat],
            ["name" => "Parvovirosis", "species_id" => $dog],
            ["name" => "Tos de las perreras", "species_id" => $dog],
        ];

        foreach ($vaccineTypes as $vaccineType) {
            VaccineType::updateOrCreate(
                ["name" => $vaccineType["name"]],
                $vaccineType
            );
        }
    }
}