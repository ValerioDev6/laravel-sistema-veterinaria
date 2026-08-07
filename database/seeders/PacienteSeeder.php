<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Owner;
use App\Models\Paciente;
use App\Models\Species;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $perro = Species::where("name", "Perro")->first()?->id;
        $gato = Species::where("name", "Gato")->first()?->id;
        $conejo = Species::where("name", "Conejo")->first()?->id;

        $labrador = Breed::where("name", "Labrador Retriever")->first()?->id;
        $poodle = Breed::where("name", "Poodle")->first()?->id;
        $bengali = Breed::where("name", "Bengalí")->first()?->id;
        $siames = Breed::where("name", "Siamés")->first()?->id;
        $holland = Breed::where("name", "Holland Lop")->first()?->id;

        $petsByDocument = [
            "45127893" => [ // María Quispe
                ["name" => "Rocky", "species" => $perro, "breed" => $labrador, "gender" => "macho", "birth" => "2021-05-12", "color" => "Dorado", "weight" => 28.5],
                ["name" => "Luna", "species" => $gato, "breed" => $bengali, "gender" => "hembra", "birth" => "2022-01-30", "color" => "Atigrado", "weight" => 4.2],
            ],
            "46223311" => [ // Jorge Ramírez
                ["name" => "Max", "species" => $perro, "breed" => $poodle, "gender" => "macho", "birth" => "2020-11-03", "color" => "Blanco", "weight" => 6.8],
            ],
            "43785690" => [ // Carmen Torres
                ["name" => "Misha", "species" => $gato, "breed" => $siames, "gender" => "hembra", "birth" => "2019-08-15", "color" => "Crema", "weight" => 3.9],
                ["name" => "Copito", "species" => $conejo, "breed" => $holland, "gender" => "macho", "birth" => "2023-02-20", "color" => "Blanco", "weight" => 1.5],
            ],
            "001876543" => [ // Luis Gutiérrez
                ["name" => "Thor", "species" => $perro, "breed" => $labrador, "gender" => "macho", "birth" => "2018-03-25", "color" => "Negro", "weight" => 30.1],
            ],
            "40112233" => [ // Ana Suyo
                ["name" => "Pelusa", "species" => $gato, "breed" => $bengali, "gender" => "hembra", "birth" => "2021-07-07", "color" => "Dorado", "weight" => 4.8],
                ["name" => "Bruno", "species" => $perro, "breed" => $poodle, "gender" => "macho", "birth" => "2022-09-18", "color" => "Café", "weight" => 5.4],
            ],
            "47221144" => [ // Renzo Castilla
                ["name" => "Simba", "species" => $gato, "breed" => $siames, "gender" => "macho", "birth" => "2020-12-01", "color" => "Pardo", "weight" => 4.5],
            ],
            "N8223441" => [ // Paola Vega
                ["name" => "Kiara", "species" => $perro, "breed" => $labrador, "gender" => "hembra", "birth" => "2023-04-10", "color" => "Dorada", "weight" => 12.0],
            ],
        ];

        foreach ($petsByDocument as $document => $pets) {
            $owner = Owner::where("n_documento", $document)->first();
            if (! $owner) {
                continue;
            }

            foreach ($pets as $pet) {
                Paciente::updateOrCreate(
                    ["owner_id" => $owner->id, "name" => $pet["name"]],
                    [
                        "species_id" => $pet["species"],
                        "breed_id" => $pet["breed"],
                        "gender" => $pet["gender"],
                        "birth_date" => $pet["birth"],
                        "color" => $pet["color"],
                        "weight" => $pet["weight"],
                    ],
                );
            }
        }
    }
}