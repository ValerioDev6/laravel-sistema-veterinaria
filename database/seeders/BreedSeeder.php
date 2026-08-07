<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    public function run(): void
    {
        $breedsBySpecies = [
            "Perro" => [
                "Labrador Retriever",
                "Pastor Alemán",
                "Golden Retriever",
                "Bulldog Francés",
                "Poodle",
                "Chihuahua",
                "Boxer",
                "Dálmata",
            ],
            "Gato" => [
                "Persa",
                "Siamés",
                "Maine Coon",
                "Bengalí",
                "Angora Turco",
                "Sphynx",
            ],
            "Conejo" => [
                "Holland Lop",
                "Rex",
                "Cabeza de León",
                "Belier",
            ],
            "Hámster" => [
                "Dorado",
                "Enano Ruso",
                "Chino",
                "Roborovski",
            ],
            "Ave" => [
                "Periquito Australiano",
                "Canario",
                "Cacatúa",
                "Guacamayo",
            ],
            "Tortuga" => [
                "Tortuga de Orejas Rojas",
                "Tortuga Rusa",
                "Tortuga Sulcata",
            ],
        ];

        foreach ($breedsBySpecies as $speciesName => $breeds) {
            $species = Species::where("name", $speciesName)->first();
            if (! $species) {
                continue;
            }

            foreach ($breeds as $breedName) {
                Breed::updateOrCreate(
                    ["species_id" => $species->id, "name" => $breedName],
                    ["name" => $breedName],
                );
            }
        }
    }
}