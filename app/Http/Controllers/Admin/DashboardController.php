<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Dashboard\ObtenerDashboardAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ObtenerDashboardAction $action): View
    {
        $selectedYear = $request->integer("year") ?: (int) now()->format("Y");

        return view("dashboard", [
            "title" => "Dashboard",
            "years" => $action->yearsDisponibles(),
            "selectedYear" => $selectedYear,
        ]);
    }
}
