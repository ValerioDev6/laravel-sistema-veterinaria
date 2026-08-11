<?php

namespace App\Actions\Citas;

use Illuminate\Support\Facades\DB;

class ObtenerDisponibilidadAction
{
    public function execute(string $fecha): array
    {
        $dayOfWeek = (int) \Carbon\Carbon::parse($fecha)->dayOfWeek;
        $step = 30;

        // 1. Todos los veterinarios con rol Veterinario (activos), via query builder.
        $veterinarios = DB::table("users as u")
            ->join("model_has_roles as mhr", "mhr.model_id", "=", "u.id")
            ->join("roles as r", "r.id", "=", "mhr.role_id")
            ->where("mhr.model_type", "=", "App\\Models\\User")
            ->where("r.name", "=", "Veterinario")
            ->where("u.is_active", "=", 1)
            ->orderBy("u.username")
            ->get(["u.id", "u.username"]);

        // 2. Horarios activos del día, agrupados por veterinario.
        $schedules = DB::table("veterinarian_schedules as vs")
            ->where("vs.day_of_week", "=", $dayOfWeek)
            ->where("vs.is_active", "=", 1)
            ->orderBy("vs.start_time")
            ->get(["vs.veterinarian_id", "vs.start_time", "vs.end_time"])
            ->groupBy("veterinarian_id");

        // 3. Horas ya ocupadas ese día por veterinario (citas activas + vacunas).
        //    appointment_time es TIME, viene como "09:30:00"; se normaliza a H:i.
        //    Las vacunas también ocupan la agenda del médico (agenda unificada).
        $ocupadas = DB::table("citas as c")
            ->select("c.veterinarian_id", "c.appointment_time")
            ->where("c.appointment_date", "=", $fecha)
            ->whereIn("c.status", ["pendiente", "confirmada"])
            ->get()
            ->mapWithKeys(
                fn($c) => [
                    $c->veterinarian_id .
                        "|" .
                        \Carbon\Carbon::parse($c->appointment_time)->format("H:i") =>
                        true,
                ],
            );

        $vacunas = DB::table("vacunas as v")
            ->select("v.veterinarian_id", "v.vaccination_time")
            ->where("v.vaccination_date", "=", $fecha)
            ->whereNotNull("v.vaccination_time")
            ->get()
            ->mapWithKeys(
                fn($v) => [
                    $v->veterinarian_id .
                        "|" .
                        \Carbon\Carbon::parse($v->vaccination_time)->format("H:i") =>
                        true,
                ],
            );

        $ocupadas = $ocupadas->union($vacunas);

        $result = [];

        foreach ($veterinarios as $vet) {
            $vetSchedules = $schedules->get($vet->id, collect());

            if ($vetSchedules->isEmpty()) {
                $result[] = $this->sinHorario($vet);
                continue;
            }

            $inicio = null;
            $fin = null;
            $slots = [];

            foreach ($vetSchedules as $schedule) {
                $start = \Carbon\Carbon::parse($schedule->start_time);
                $end = \Carbon\Carbon::parse($schedule->end_time);

                if ($inicio === null) {
                    $inicio = $start->format("H:i");
                }
                $fin = $end->format("H:i");

                $cursor = $start->copy();
                while ($cursor->lt($end)) {
                    $hora = $cursor->format("H:i");
                    $key = $vet->id . "|" . $hora;
                    $ocupado = isset($ocupadas[$key]);

                    $slots[] = [
                        "hora" => $hora,
                        "ocupado" => $ocupado,
                        "disponible" => ! $ocupado,
                    ];
                    $cursor->addMinutes($step);
                }
            }

            $libres = array_values(array_unique(array_column(
                array_filter($slots, fn($s) => $s["disponible"]),
                "hora",
            )));
            sort($libres);

            $result[] = [
                "veterinarian_id" => $vet->id,
                "veterinarian" => $vet->username,
                "start_time" => $inicio,
                "end_time" => $fin,
                "horario" => $inicio . " – " . $fin,
                "slots" => $libres,
                "slots_detalle" => $slots,
                "estado" => count($libres) > 0 ? "disponible" : "ocupado",
                "disponible" => count($libres) > 0,
            ];
        }

        return $result;
    }

    private function sinHorario($vet): array
    {
        return [
            "veterinarian_id" => $vet->id,
            "veterinarian" => $vet->username,
            "start_time" => null,
            "end_time" => null,
            "horario" => "Sin horario este día",
            "slots" => [],
            "slots_detalle" => [],
            "estado" => "sin_horario",
            "disponible" => false,
        ];
    }
}
