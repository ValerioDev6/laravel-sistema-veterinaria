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
            ["name" => "Séxtuple canina", "species_id" => $dog, "base_price" => 60.00],
            ["name" => "Rabia canina", "species_id" => $dog, "base_price" => 35.00],
            ["name" => "Triple felina", "species_id" => $cat, "base_price" => 55.00],
            ["name" => "Rabia felina", "species_id" => $cat, "base_price" => 35.00],
            ["name" => "Leucemia felina", "species_id" => $cat, "base_price" => 70.00],
            ["name" => "Parvovirosis", "species_id" => $dog, "base_price" => 50.00],
            ["name" => "Tos de las perreras", "species_id" => $dog, "base_price" => 45.00],
        ];

        foreach ($vaccineTypes as $vaccineType) {
            VaccineType::updateOrCreate(
                ["name" => $vaccineType["name"]],
                $vaccineType
            );
        }
    }
}