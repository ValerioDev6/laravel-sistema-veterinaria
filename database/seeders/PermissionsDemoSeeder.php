<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * NOTA: guard_name = "api" porque el propio Blade consume esta API
     * via AJAX/fetch (para evitar recargas de pagina), no via sesion tradicional.
     * Esto implica que necesitas Sanctum o Passport configurado, y que
     * tu User model tenga el trait correspondiente (HasApiTokens de Sanctum).
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = "api";

        // ---------------------------------------------------------------
        // ROLES Y USUARIOS (Spatie)
        // ---------------------------------------------------------------
        Permission::create(["guard_name" => $guard, "name" => "register_rol"]);
        Permission::create(["guard_name" => $guard, "name" => "list_rol"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_rol"]);
        Permission::create(["guard_name" => $guard, "name" => "delete_rol"]);

        // ---------------------------------------------------------------
        // STAFF / VETERINARIOS (tabla users)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_staff",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_staff"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_staff"]);
        Permission::create(["guard_name" => $guard, "name" => "delete_staff"]);
        Permission::create(["guard_name" => $guard, "name" => "profile_staff"]);

        // ---------------------------------------------------------------
        // BRANCHES (sedes)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_branch",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_branch"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_branch"]);
        Permission::create(["guard_name" => $guard, "name" => "delete_branch"]);

        // ---------------------------------------------------------------
        // VETERINARIAN_SCHEDULES (horario del vet)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_schedule",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_schedule"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_schedule"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_schedule",
        ]);

        // ---------------------------------------------------------------
        // OWNERS (duenos de mascotas)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_owner",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_owner"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_owner"]);
        Permission::create(["guard_name" => $guard, "name" => "delete_owner"]);

        // ---------------------------------------------------------------
        // PACIENTES (mascotas) + species/breeds
        // ---------------------------------------------------------------
        Permission::create(["guard_name" => $guard, "name" => "register_pet"]);
        Permission::create(["guard_name" => $guard, "name" => "list_pet"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_pet"]);
        Permission::create(["guard_name" => $guard, "name" => "delete_pet"]);
        Permission::create(["guard_name" => $guard, "name" => "profile_pet"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "manage_species_breeds",
        ]);

        // ---------------------------------------------------------------
        // CITAS (appointments)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_appointment",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "list_appointment",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "edit_appointment",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_appointment",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "calendar"]);

        // ---------------------------------------------------------------
        // SERVICES (catalogo de servicios/precios)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_service",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_service"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_service"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_service",
        ]);

        // ---------------------------------------------------------------
        // VACUNAS (vaccine_types + vacunas)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_vaccination",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "list_vaccination",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "edit_vaccination",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_vaccination",
        ]);

        // ---------------------------------------------------------------
        // SURGIERE (cirugias)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_surgery",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_surgery"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_surgery"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_surgery",
        ]);

        // ---------------------------------------------------------------
        // HISTORIAL MEDICO (medical_record, attachments, vital_signs, prescriptions)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "show_medical_records",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "edit_medical_records",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "manage_prescriptions",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "manage_vital_signs",
        ]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "manage_attachments",
        ]);

        // ---------------------------------------------------------------
        // MEDICINES (inventario/farmacia)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_medicine",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "list_medicine"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_medicine"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "delete_medicine",
        ]);

        // ---------------------------------------------------------------
        // INVOICES + PAYMENTS (facturacion)
        // ---------------------------------------------------------------
        Permission::create(["guard_name" => $guard, "name" => "show_invoice"]);
        Permission::create(["guard_name" => $guard, "name" => "edit_invoice"]);
        Permission::create(["guard_name" => $guard, "name" => "show_payment"]);
        Permission::create([
            "guard_name" => $guard,
            "name" => "register_payment",
        ]);
        Permission::create(["guard_name" => $guard, "name" => "edit_payment"]);

        // ---------------------------------------------------------------
        // REMINDERS (recordatorios)
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "manage_reminders",
        ]);

        // ---------------------------------------------------------------
        // REPORTES
        // ---------------------------------------------------------------
        Permission::create([
            "guard_name" => $guard,
            "name" => "show_report_grafics",
        ]);

        // =================================================================
        // ROLES
        // =================================================================

        // Super-Admin: obtiene TODOS los permisos via Gate::before (AuthServiceProvider)
        $superAdmin = Role::create([
            "guard_name" => $guard,
            "name" => "Super-Admin",
        ]);

        // Veterinario: solo lo clinico, no gestiona staff/roles/facturacion completa
        $veterinario = Role::create([
            "guard_name" => $guard,
            "name" => "Veterinario",
        ]);
        $veterinario->givePermissionTo([
            "list_pet",
            "profile_pet",
            "list_appointment",
            "edit_appointment",
            "calendar",
            "register_vaccination",
            "list_vaccination",
            "edit_vaccination",
            "register_surgery",
            "list_surgery",
            "edit_surgery",
            "show_medical_records",
            "edit_medical_records",
            "manage_prescriptions",
            "manage_vital_signs",
            "manage_attachments",
            "list_schedule",
            "edit_schedule",
            "list_owner",
        ]);

        // Recepcionista: agenda, pacientes, duenos, pagos basicos - sin acceso clinico
        $recepcionista = Role::create([
            "guard_name" => $guard,
            "name" => "Recepcionista",
        ]);
        $recepcionista->givePermissionTo([
            "register_owner",
            "list_owner",
            "edit_owner",
            "register_pet",
            "list_pet",
            "edit_pet",
            "register_appointment",
            "list_appointment",
            "edit_appointment",
            "calendar",
            "show_invoice",
            "show_payment",
            "register_payment",
            "manage_reminders",
        ]);

        // Usuario administrador de prueba
        $admin = User::factory()->create([
            "username" => "admin",
            "email" => "admin@gmail.com",
            "password" => bcrypt("12345678"),
        ]);
        $admin->assignRole($superAdmin);
    }
}
