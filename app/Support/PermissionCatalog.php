<?php

namespace App\Support;

class PermissionCatalog
{
    /**
     * Permisos agrupados por módulo, en orden de presentación.
     * Los permisos que no aparezcan aquí caen en el grupo "Otros".
     *
     * @return array<string, array<int, string>>
     */
    public static function groups(): array
    {
        return [
            "Roles" => ["register_rol", "list_rol", "edit_rol", "delete_rol"],
            "Personal" => ["register_staff", "list_staff", "edit_staff", "delete_staff", "profile_staff"],
            "Sucursales" => ["register_branch", "list_branch", "edit_branch", "delete_branch"],
            "Horarios" => ["register_schedule", "list_schedule", "edit_schedule", "delete_schedule"],
            "Propietarios" => ["register_owner", "list_owner", "edit_owner", "delete_owner"],
            "Pacientes" => ["register_pet", "list_pet", "edit_pet", "delete_pet", "profile_pet"],
            "Especies y razas" => ["manage_species_breeds"],
            "Servicios" => ["register_service", "list_service", "edit_service", "delete_service"],
            "Citas" => ["register_appointment", "list_appointment", "edit_appointment", "delete_appointment", "calendar"],
            "Vacunas" => ["register_vaccination", "list_vaccination", "edit_vaccination", "delete_vaccination"],
            "Cirugías" => ["register_surgery", "list_surgery", "edit_surgery", "delete_surgery"],
            "Historial clínico" => ["show_medical_records", "edit_medical_records", "manage_prescriptions", "manage_vital_signs", "manage_attachments"],
            "Medicamentos" => ["register_medicine", "list_medicine", "edit_medicine", "delete_medicine"],
            "Facturación" => ["show_invoice", "edit_invoice", "show_payment", "register_payment", "edit_payment"],
            "Recordatorios" => ["manage_reminders"],
            "Reportes" => ["show_report_grafics"],
        ];
    }

    /**
     * Etiqueta legible en español por permiso.
     *
     * @var array<string, string>
     */
    protected static array $labels = [
        "register_rol" => "Registrar rol",
        "list_rol" => "Listar roles",
        "edit_rol" => "Editar rol",
        "delete_rol" => "Eliminar rol",
        "register_staff" => "Registrar personal",
        "list_staff" => "Listar personal",
        "edit_staff" => "Editar personal",
        "delete_staff" => "Eliminar personal",
        "profile_staff" => "Ver perfil de personal",
        "register_branch" => "Registrar sucursal",
        "list_branch" => "Listar sucursales",
        "edit_branch" => "Editar sucursal",
        "delete_branch" => "Eliminar sucursal",
        "register_schedule" => "Registrar horario",
        "list_schedule" => "Listar horarios",
        "edit_schedule" => "Editar horario",
        "delete_schedule" => "Eliminar horario",
        "register_owner" => "Registrar propietario",
        "list_owner" => "Listar propietarios",
        "edit_owner" => "Editar propietario",
        "delete_owner" => "Eliminar propietario",
        "register_pet" => "Registrar paciente",
        "list_pet" => "Listar pacientes",
        "edit_pet" => "Editar paciente",
        "delete_pet" => "Eliminar paciente",
        "profile_pet" => "Ver perfil de paciente",
        "manage_species_breeds" => "Gestionar especies y razas",
        "register_appointment" => "Registrar cita",
        "list_appointment" => "Listar citas",
        "edit_appointment" => "Editar cita",
        "delete_appointment" => "Eliminar cita",
        "calendar" => "Ver calendario",
        "register_service" => "Registrar servicio",
        "list_service" => "Listar servicios",
        "edit_service" => "Editar servicio",
        "delete_service" => "Eliminar servicio",
        "register_vaccination" => "Registrar vacunación",
        "list_vaccination" => "Listar vacunaciones",
        "edit_vaccination" => "Editar vacunación",
        "delete_vaccination" => "Eliminar vacunación",
        "register_surgery" => "Registrar cirugía",
        "list_surgery" => "Listar cirugías",
        "edit_surgery" => "Editar cirugía",
        "delete_surgery" => "Eliminar cirugía",
        "show_medical_records" => "Ver historial médico",
        "edit_medical_records" => "Editar historial médico",
        "manage_prescriptions" => "Gestionar recetas",
        "manage_vital_signs" => "Gestionar signos vitales",
        "manage_attachments" => "Gestionar adjuntos",
        "register_medicine" => "Registrar medicamento",
        "list_medicine" => "Listar medicamentos",
        "edit_medicine" => "Editar medicamento",
        "delete_medicine" => "Eliminar medicamento",
        "show_invoice" => "Ver facturas",
        "edit_invoice" => "Editar factura",
        "show_payment" => "Ver pagos",
        "register_payment" => "Registrar pago",
        "edit_payment" => "Editar pago",
        "manage_reminders" => "Gestionar recordatorios",
        "show_report_grafics" => "Ver reportes y gráficas",
    ];

    public static function groupFor(string $permission): string
    {
        foreach (static::groups() as $group => $names) {
            if (in_array($permission, $names, true)) {
                return $group;
            }
        }

        return "Otros";
    }

    public static function labelFor(string $permission): string
    {
        return static::$labels[$permission] ?? $permission;
    }

    /**
     * @return array<int, string>
     */
    public static function allKnown(): array
    {
        return array_values(array_unique(array_merge(...array_values(static::groups()))));
    }
}
