<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $owners = [
            [
                "first_name" => "María", "last_name" => "Quispe Huamán",
                "email" => "maria.quispe@gmail.com", "phone" => "987654321",
                "address" => "Av. Los Alamos 1234", "city" => "Miraflores",
                "type_documento" => "DNI", "n_documento" => "45127893",
            ],
            [
                "first_name" => "Jorge", "last_name" => "Ramírez Salas",
                "email" => "jorge.ramirez@gmail.com", "phone" => "998123456",
                "address" => "Jr. Las Palmeras 456", "city" => "San Isidro",
                "type_documento" => "DNI", "n_documento" => "46223311",
            ],
            [
                "first_name" => "Carmen", "last_name" => "Torres Lozano",
                "email" => "carmen.torres@hotmail.com", "phone" => "965432178",
                "address" => "Calle Los Pinos 789", "city" => "La Molina",
                "type_documento" => "DNI", "n_documento" => "43785690",
            ],
            [
                "first_name" => "Luis", "last_name" => "Gutiérrez Paz",
                "email" => "luis.gutierrez@gmail.com", "phone" => "976543210",
                "address" => "Av. Benavides 987", "city" => "Miraflores",
                "type_documento" => "CE", "n_documento" => "001876543",
            ],
            [
                "first_name" => "Ana", "last_name" => "Suyo Mendoza",
                "email" => "ana.suyo@gmail.com", "phone" => "912345678",
                "address" => "Jr. Unión 345", "city" => "San Isidro",
                "type_documento" => "DNI", "n_documento" => "40112233",
            ],
            [
                "first_name" => "Renzo", "last_name" => "Castilla Rojas",
                "email" => "renzo.castilla@gmail.com", "phone" => "954321876",
                "address" => "Av. La Marina 1122", "city" => "La Molina",
                "type_documento" => "DNI", "n_documento" => "47221144",
            ],
            [
                "first_name" => "Paola", "last_name" => "Vega Núñez",
                "email" => "paola.vega@gmail.com", "phone" => "987123654",
                "address" => "Calle Los Tulipanes 220", "city" => "Miraflores",
                "type_documento" => "Pasaporte", "n_documento" => "N8223441",
            ],
        ];

        foreach ($owners as $owner) {
            Owner::updateOrCreate(
                ["n_documento" => $owner["n_documento"]],
                $owner
            );
        }
    }
}