<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SessionApiAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            "database.default" => "mysql",
            "database.connections.mysql.database" => "db_sistema_veterinario",
            "database.connections.mysql.host" => "127.0.0.1",
            "database.connections.mysql.port" => "3306",
            "database.connections.mysql.username" => "root",
            "database.connections.mysql.password" => "admin",
            "session.driver" => "database",
            "sanctum.stateful" => [
                "localhost",
                "127.0.0.1:8000",
            ],
        ]);
    }

    private function autenticar(): void
    {
        $this->post("/login", [
            "email" => "admin@gmail.com",
            "password" => "12345678",
        ])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    private function headersApi(): array
    {
        return [
            "Accept" => "application/json",
            "X-Requested-With" => "XMLHttpRequest",
            "Origin" => "http://localhost",
        ];
    }

    private function slotLibre(): array
    {
        $schedules = \App\Models\VeterinarianSchedule::where("is_active", true)
            ->orderBy("veterinarian_id")
            ->orderBy("day_of_week")
            ->get();

        $now = \Carbon\Carbon::now();
        foreach ($schedules as $schedule) {
            $dia = $now->copy();
            while ((int) $dia->dayOfWeek !== (int) $schedule->day_of_week) {
                $dia->addDay();
            }
            $fecha = $dia->format("Y-m-d");

            $hora = \Carbon\Carbon::parse($schedule->start_time);
            $fin = \Carbon\Carbon::parse($schedule->end_time);
            while ($hora->lt($fin)) {
                $h = $hora->format("H:i");
                $conflicto = \App\Models\Cita::where(
                    "veterinarian_id",
                    $schedule->veterinarian_id,
                )
                    ->where("appointment_date", $fecha)
                    ->where("appointment_time", $h)
                    ->whereIn("status", ["pendiente", "confirmada"])
                    ->exists();

                if (! $conflicto) {
                    return [
                        "veterinarian_id" => $schedule->veterinarian_id,
                        "fecha" => $fecha,
                        "hora" => $h,
                    ];
                }
                $hora->addMinutes(30);
            }
        }

        $this->fail("No se encontró un slot libre para crear la cita de prueba.");
    }

    public function test_login_web_y_peticion_api_resuelven_sesion(): void
    {
        $this->autenticar();
        auth()->forgetGuards();

        $resp = $this->withHeaders($this->headersApi())->getJson("/api/user");

        $resp->assertOk();
        $this->assertSame("admin", $resp->json("username"));
    }

    public function test_cita_creada_por_http_registra_created_by_user_id(): void
    {
        $this->autenticar();
        auth()->forgetGuards();

        $slot = $this->slotLibre();

        $resp = $this->withHeaders($this->headersApi())->postJson(
            "/api/admin/citas",
            [
                "pet_id" => 1,
                "veterinarian_id" => $slot["veterinarian_id"],
                "service_id" => 1,
                "appointment_date" => $slot["fecha"],
                "appointment_time" => $slot["hora"],
                "reason" => "test created_by",
                "status" => "pendiente",
                "payment_method" => "efectivo",
                "advance_amount" => 10,
            ],
        );

        $resp->assertStatus(201);
        $citaId = $resp->json("data.id");
        $cita = \App\Models\Cita::find($citaId);
        $this->assertSame(
            1,
            $cita->created_by_user_id,
            "El created_by_user_id debe ser el usuario autenticado (admin id=1). Actual: ".var_export($cita->created_by_user_id, true),
        );
    }
}
