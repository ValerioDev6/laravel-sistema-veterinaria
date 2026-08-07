<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ["name" => "Consulta general", "category" => "consulta", "base_price" => 60.00, "duration_minutes" => 30],
            ["name" => "Consulta especializada", "category" => "consulta", "base_price" => 90.00, "duration_minutes" => 45],
            ["name" => "Vacunación", "category" => "vacunacion", "base_price" => 40.00, "duration_minutes" => 20],
            ["name" => "Desparasitación", "category" => "consulta", "base_price" => 35.00, "duration_minutes" => 20],
            ["name" => "Esterilización", "category" => "cirugia", "base_price" => 350.00, "duration_minutes" => 90],
            ["name" => "Baño y corte", "category" => "estetica", "base_price" => 70.00, "duration_minutes" => 60],
            ["name" => "Cirugía general", "category" => "cirugia", "base_price" => 500.00, "duration_minutes" => 120],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ["name" => $service["name"]],
                $service
            );
        }
    }
}