<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PermissionCatalog;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $grupos = array_keys(PermissionCatalog::groups());
        $grupos[] = "Otros";

        return view("admin.permisos.index", [
            "title" => "Permisos",
            "grupos" => $grupos,
        ]);
    }
}
