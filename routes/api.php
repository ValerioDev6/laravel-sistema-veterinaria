<?php

use App\Http\Controllers\Api\Admin\BranchController;
use App\Http\Controllers\Api\Admin\BreedController;
use App\Http\Controllers\Api\Admin\CalendarioController;
use App\Http\Controllers\Api\Admin\CitaController;
use App\Http\Controllers\Api\Admin\CirugiaController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\MedicalRecordAttachmentController;
use App\Http\Controllers\Api\Admin\MedicalRecordController;
use App\Http\Controllers\Api\Admin\MedicineController;
use App\Http\Controllers\Api\Admin\OwnerController;
use App\Http\Controllers\Api\Admin\PacienteController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\SpeciesController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\VacunaController;
use App\Http\Controllers\Api\Admin\VaccineTypeController;
use App\Http\Controllers\Api\Admin\VeterinarianScheduleController;
use Illuminate\Support\Facades\Route;

Route::get("/user", function (Illuminate\Http\Request $request) {
    return $request->user();
})->middleware("auth:api");

// Route::middleware(["auth:api])
Route::prefix("admin")
    ->name("admin.api.")
    ->group(function () {
        Route::get("calendario", [
            CalendarioController::class,
            "index",
        ])->name("calendario");

        Route::apiResource("branches", BranchController::class);
        Route::apiResource("species", SpeciesController::class);
        Route::apiResource("breeds", BreedController::class);
        Route::apiResource("services", ServiceController::class);
        Route::apiResource("vaccine-types", VaccineTypeController::class);
        Route::apiResource("medicines", MedicineController::class);
        Route::apiResource("owners", OwnerController::class);
        Route::apiResource("pacientes", PacienteController::class);
        Route::get("pacientes/{paciente}/records", [
            PacienteController::class,
            "records",
        ])->name("pacientes.records");
        Route::get("pacientes/{paciente}/vacunas", [
            PacienteController::class,
            "vacunas",
        ])->name("pacientes.vacunas");
        Route::get("pacientes/{paciente}/cirugias", [
            PacienteController::class,
            "cirugias",
        ])->name("pacientes.cirugias");

        Route::apiResource(
            "veterinarian-schedules",
            VeterinarianScheduleController::class,
        );

        Route::apiResource("citas", CitaController::class)->only([
            "index",
            "store",
            "update",
            "destroy",
        ]);
        Route::get("citas/disponibilidad", [
            CitaController::class,
            "disponibilidad",
        ])->name("citas.disponibilidad");
        Route::get("citas/calendario", [
            CitaController::class,
            "calendario",
        ])->name("citas.calendario");
        Route::patch("citas/{cita}/estado", [
            CitaController::class,
            "cambiarEstado",
        ])->name("citas.estado");

        Route::get("vacunas/disponibilidad", [
            VacunaController::class,
            "disponibilidad",
        ])->name("vacunas.disponibilidad");
        Route::patch("vacunas/{vacuna}/estado-pago", [
            VacunaController::class,
            "cambiarEstadoPago",
        ])->name("vacunas.estado-pago");
        Route::apiResource("vacunas", VacunaController::class);

        Route::apiResource("cirugias", CirugiaController::class)->only([
            "index",
            "store",
            "update",
            "destroy",
        ]);
        Route::patch("cirugias/{cirugia}/estado", [
            CirugiaController::class,
            "cambiarEstado",
        ])->name("cirugias.estado");

        Route::apiResource("medical-records", MedicalRecordController::class);
        Route::post("medical-records/{medicalRecord}/adjuntos", [
            MedicalRecordAttachmentController::class,
            "store",
        ])->name("medical-records.adjuntos.store");
        Route::delete("adjuntos/{medicalRecordAttachment}", [
            MedicalRecordAttachmentController::class,
            "destroy",
        ])->name("adjuntos.destroy");

        Route::apiResource("invoices", InvoiceController::class)->only([
            "index",
            "store",
            "show",
            "update",
        ]);
        Route::patch("invoices/{invoice}/anular", [
            InvoiceController::class,
            "anular",
        ])->name("invoices.anular");
        Route::post("invoices/{invoice}/payments", [
            PaymentController::class,
            "store",
        ])->name("invoices.payments.store");
        Route::patch("payments/{payment}/anular", [
            PaymentController::class,
            "anular",
        ])->name("payments.anular");
        Route::get("payments", [
            PaymentController::class,
            "index",
        ])->name("payments.index");

        Route::apiResource("users", UserController::class)->only([
            "index",
            "store",
            "update",
            "destroy",
        ]);
        Route::patch("users/{user}/toggle-status", [
            UserController::class,
            "toggleStatus",
        ])->name("users.toggle-status");
    });
