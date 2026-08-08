<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BreedController;
use App\Http\Controllers\Admin\CitaController;
use App\Http\Controllers\Admin\CirugiaController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PacienteController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SpeciesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VacunaController;
use App\Http\Controllers\Admin\VaccineTypeController;
use App\Http\Controllers\Admin\VeterinarianScheduleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

Route::middleware("auth")
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("branches", [BranchController::class, "index"])->name(
            "branches.index",
        );
        Route::get("branches/create", [
            BranchController::class,
            "create",
        ])->name("branches.create");
        Route::get("branches/{branch}/edit", [
            BranchController::class,
            "edit",
        ])->name("branches.edit");

        Route::get("usuarios", [UserController::class, "index"])->name(
            "usuarios.index",
        );
        Route::get("usuarios/create", [UserController::class, "create"])->name(
            "usuarios.create",
        );
        Route::get("usuarios/{user}/edit", [
            UserController::class,
            "edit",
        ])->name("usuarios.edit");

        Route::get("species", [SpeciesController::class, "index"])->name(
            "species.index",
        );
        Route::get("species/create", [
            SpeciesController::class,
            "create",
        ])->name("species.create");
        Route::get("species/{species}/edit", [
            SpeciesController::class,
            "edit",
        ])->name("species.edit");

        Route::get("breeds", [BreedController::class, "index"])->name(
            "breeds.index",
        );
        Route::get("breeds/create", [BreedController::class, "create"])->name(
            "breeds.create",
        );
        Route::get("breeds/{breed}/edit", [
            BreedController::class,
            "edit",
        ])->name("breeds.edit");

        Route::get("services", [ServiceController::class, "index"])->name(
            "services.index",
        );
        Route::get("services/create", [
            ServiceController::class,
            "create",
        ])->name("services.create");
        Route::get("services/{service}/edit", [
            ServiceController::class,
            "edit",
        ])->name("services.edit");

        Route::get("vaccine-types", [
            VaccineTypeController::class,
            "index",
        ])->name("vaccine-types.index");
        Route::get("vaccine-types/create", [
            VaccineTypeController::class,
            "create",
        ])->name("vaccine-types.create");
        Route::get("vaccine-types/{vaccine_type}/edit", [
            VaccineTypeController::class,
            "edit",
        ])->name("vaccine-types.edit");

        Route::get("medicines", [MedicineController::class, "index"])->name(
            "medicines.index",
        );
        Route::get("medicines/create", [
            MedicineController::class,
            "create",
        ])->name("medicines.create");
        Route::get("medicines/{medicine}/edit", [
            MedicineController::class,
            "edit",
        ])->name("medicines.edit");

        Route::get("owners", [OwnerController::class, "index"])->name(
            "owners.index",
        );
        Route::get("owners/{owner}", [OwnerController::class, "show"])->name(
            "owners.show",
        );

        Route::get("pacientes", [PacienteController::class, "index"])->name(
            "pacientes.index",
        );
        Route::get("pacientes/create", [
            PacienteController::class,
            "create",
        ])->name("pacientes.create");
        Route::get("pacientes/{paciente}/edit", [
            PacienteController::class,
            "edit",
        ])->name("pacientes.edit");
        Route::get("pacientes/{paciente}", [
            PacienteController::class,
            "show",
        ])->name("pacientes.show");

        Route::get("veterinarian-schedules", [
            VeterinarianScheduleController::class,
            "index",
        ])->name("veterinarian-schedules.index");
        Route::get("veterinarian-schedules/create", [
            VeterinarianScheduleController::class,
            "create",
        ])->name("veterinarian-schedules.create");
        Route::get("veterinarian-schedules/{veterinarian_schedule}/edit", [
            VeterinarianScheduleController::class,
            "edit",
        ])->name("veterinarian-schedules.edit");

        Route::get("citas", [CitaController::class, "index"])->name(
            "citas.index",
        );
        Route::get("citas/create", [CitaController::class, "create"])->name(
            "citas.create",
        );
        Route::get("citas/{cita}/edit", [CitaController::class, "edit"])->name(
            "citas.edit",
        );
        Route::get("citas/calendario", [
            CitaController::class,
            "calendar",
        ])->name("citas.calendar");

        Route::get("vacunas", [VacunaController::class, "index"])->name(
            "vacunas.index",
        );
        Route::get("vacunas/create", [VacunaController::class, "create"])->name(
            "vacunas.create",
        );
        Route::get("vacunas/{vacuna}/edit", [
            VacunaController::class,
            "edit",
        ])->name("vacunas.edit");

        Route::get("cirugias", [CirugiaController::class, "index"])->name(
            "cirugias.index",
        );
        Route::get("cirugias/create", [
            CirugiaController::class,
            "create",
        ])->name("cirugias.create");
        Route::get("cirugias/{cirugia}/edit", [
            CirugiaController::class,
            "edit",
        ])->name("cirugias.edit");

        Route::get("medical-records", [
            MedicalRecordController::class,
            "index",
        ])->name("medical-records.index");
        Route::get("medical-records/create", [
            MedicalRecordController::class,
            "create",
        ])->name("medical-records.create");
        Route::get("medical-records/{medicalRecord}", [
            MedicalRecordController::class,
            "show",
        ])->name("medical-records.show");

        Route::get("invoices", [InvoiceController::class, "index"])->name(
            "invoices.index",
        );
        Route::get("invoices/create", [
            InvoiceController::class,
            "create",
        ])->name("invoices.create");
        Route::get("invoices/{invoice}", [
            InvoiceController::class,
            "show",
        ])->name("invoices.show");
    });

require __DIR__ . "/auth.php";
