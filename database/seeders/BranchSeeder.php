<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                "name" => "Sede Miraflores",
                "address" => "Av. José Larco 1234, Miraflores",
                "city" => "Lima",
                "phone" => "(01) 445-7890",
            ],
            [
                "name" => "Sede San Isidro",
                "address" => "Av. Javier Prado Este 456, San Isidro",
                "city" => "Lima",
                "phone" => "(01) 221-3344",
            ],
            [
                "name" => "Sede La Molina",
                "address" => "Av. La Molina 789, La Molina",
                "city" => "Lima",
                "phone" => "(01) 349-8877",
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(["name" => $branch["name"]], $branch);
        }
    }
}