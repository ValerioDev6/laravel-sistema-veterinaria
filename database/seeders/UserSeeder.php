<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            "Sede Miraflores" => Branch::where("name", "Sede Miraflores")->first()?->id,
            "Sede San Isidro" => Branch::where("name", "Sede San Isidro")->first()?->id,
            "Sede La Molina" => Branch::where("name", "Sede La Molina")->first()?->id,
        ];

        $staff = [
            [
                "branch_id" => $branches["Sede Miraflores"],
                "username" => "dr.torres",
                "email" => "carlos.torres@veterinaria.com",
                "role" => "Veterinario",
            ],
            [
                "branch_id" => $branches["Sede San Isidro"],
                "username" => "dr.ramos",
                "email" => "lucia.ramos@veterinaria.com",
                "role" => "Veterinario",
            ],
            [
                "branch_id" => $branches["Sede La Molina"],
                "username" => "dr.paredes",
                "email" => "jorge.paredes@veterinaria.com",
                "role" => "Veterinario",
            ],
            [
                "branch_id" => $branches["Sede Miraflores"],
                "username" => "ana.suy",
                "email" => "ana.suy@veterinaria.com",
                "role" => "Recepcionista",
            ],
            [
                "branch_id" => $branches["Sede San Isidro"],
                "username" => "renzo.castilla",
                "email" => "renzo.castilla@veterinaria.com",
                "role" => "Recepcionista",
            ],
        ];

        foreach ($staff as $item) {
            $role = $item["role"];
            unset($item["role"]);

            $user = User::updateOrCreate(
                ["email" => $item["email"]],
                array_merge($item, [
                    "username" => $item["username"],
                    "password" => bcrypt("12345678"),
                    "is_active" => true,
                ]),
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}