<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ["name" => "Amoxicilina 500 mg", "quantity" => 50, "unit_cost" => 12.5],
            ["name" => "Meloxicam 15 mg", "quantity" => 40, "unit_cost" => 8.9],
            ["name" => "Ivermectina 1%", "quantity" => 30, "unit_cost" => 15.0],
            ["name" => "Metronidazol 250 mg", "quantity" => 35, "unit_cost" => 10.2],
            ["name" => "Suero fisiológico 500 ml", "quantity" => 60, "unit_cost" => 5.8],
            ["name" => "Prednisolona 20 mg", "quantity" => 45, "unit_cost" => 9.5],
        ];

        foreach ($medicines as $medicine) {
            Medicine::updateOrCreate(
                ["name" => $medicine["name"]],
                $medicine
            );
        }
    }
}