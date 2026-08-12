<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsDemoSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            SpeciesSeeder::class,
            BreedSeeder::class,
            ServiceSeeder::class,
            VaccineTypeSeeder::class,
            MedicineSeeder::class,
            OwnerSeeder::class,
            PacienteSeeder::class,
            VeterinarianScheduleSeeder::class,
            CitaSeeder::class,
            VacunaSeeder::class,
            CirugiaSeeder::class,
            MedicalRecordSeeder::class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            ReminderSeeder::class,
        ]);
    }
}