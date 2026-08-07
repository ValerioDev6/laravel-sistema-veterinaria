<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = ["Perro", "Gato", "Conejo", "Hámster", "Ave", "Tortuga"];

        foreach ($species as $name) {
            Species::updateOrCreate(["name" => $name], ["name" => $name]);
        }
    }
}