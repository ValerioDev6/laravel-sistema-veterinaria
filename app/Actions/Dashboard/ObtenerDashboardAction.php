<?php

namespace App\Actions\Dashboard;

use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Paciente;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\Species;
use App\Models\Surgiere;
use App\Models\Vacuna;
use Carbon\Carbon;

class ObtenerDashboardAction
{
    private const ESTADOS_CITA = [
        "pendiente" => "Pendiente",
        "confirmada" => "Confirmada",
        "completada" => "Completada",
        "cancelada" => "Cancelada",
    ];

    private const MESES = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

    public function execute(?int $year): array
    {
        $year ??= (int) now()->format("Y");

        return [
            "kpis" => $this->kpis($year),
            "charts" => $this->charts($year),
        ];
    }

    public function yearsDisponibles(): array
    {
        $years = collect([
            Invoice::pluck("issued_at"),
            Cita::pluck("appointment_date"),
            Vacuna::pluck("vaccination_date"),
            Surgiere::pluck("surgery_date"),
        ])
            ->flatten()
            ->filter()
            ->map(fn ($date) => (int) Carbon::parse($date)->format("Y"))
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        return empty($years) ? [(int) now()->format("Y")] : $years;
    }

    private function kpis(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = Carbon::create($year, 12, 31)->endOfYear();

        return [
            "facturado" => (float) Invoice::whereBetween("issued_at", [$start, $end])->sum("total"),
            "cobrado" => (float) Payment::whereBetween("paid_at", [$start, $end])->sum("amount"),
            "por_cobrar" => (float) Invoice::whereIn("status", ["pendiente", "parcial"])->sum("remaining_balance"),
            "citas_hoy" => Cita::whereDate("appointment_date", now()->toDateString())
                ->where("status", "!=", "cancelada")
                ->count(),
            "pacientes" => Paciente::count(),
            "vacunas" => Vacuna::whereBetween("vaccination_date", [$start, $end])->count(),
            "cirugias" => Surgiere::whereBetween("surgery_date", [$start, $end])->count(),
            "recordatorios_pendientes" => Reminder::where("status", "pendiente")->count(),
        ];
    }

    private function charts(int $year): array
    {
        return [
            "ingresos_mensuales" => $this->ingresosMensuales($year),
            "citas_por_estado" => $this->citasPorEstado($year),
            "pacientes_por_especie" => $this->pacientesPorEspecie(),
            "top_veterinarios" => $this->topVeterinarios($year),
        ];
    }

    private function ingresosMensuales(int $year): array
    {
        $labels = [];
        $emitido = [];
        $cobrado = [];

        foreach (range(1, 12) as $month) {
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

            $labels[] = self::MESES[$month - 1];
            $emitido[] = (float) Invoice::whereBetween("issued_at", [$monthStart, $monthEnd])->sum("total");
            $cobrado[] = (float) Payment::whereBetween("paid_at", [$monthStart, $monthEnd])->sum("amount");
        }

        return compact("labels", "emitido", "cobrado");
    }

    private function citasPorEstado(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = Carbon::create($year, 12, 31)->endOfYear();

        $counts = Cita::whereBetween("appointment_date", [$start, $end])
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy("status")
            ->pluck("total", "status")
            ->toArray();

        $labels = [];
        $data = [];

        foreach (self::ESTADOS_CITA as $key => $label) {
            $labels[] = $label;
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return compact("labels", "data");
    }

    private function pacientesPorEspecie(): array
    {
        $species = Species::withCount("pacientes")->get();

        return [
            "labels" => $species->pluck("name")->values()->toArray(),
            "data" => $species->pluck("pacientes_count")->values()->map(fn ($n) => (int) $n)->toArray(),
        ];
    }

    private function topVeterinarios(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = Carbon::create($year, 12, 31)->endOfYear();

        $top = Cita::whereBetween("appointment_date", [$start, $end])
            ->where("status", "!=", "cancelada")
            ->with("veterinarian")
            ->get()
            ->groupBy("veterinarian_id")
            ->map(fn ($citas) => [
                "veterinario" => $citas->first()->veterinarian?->username ?? "Sin asignar",
                "total" => $citas->count(),
            ])
            ->sortByDesc("total")
            ->values()
            ->take(5)
            ->values()
            ->toArray();

        return [
            "labels" => array_column($top, "veterinario"),
            "data" => array_map(fn ($row) => (int) $row["total"], $top),
        ];
    }
}
