<?php

namespace App\Actions\Users;

use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListUsersAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(User::with('branch'))
            ->through([
                new FiltrarPorBusqueda($request, ['username', 'email']),
                new OrdenarPor($request, [0 => 'id', 1 => 'username', 2 => 'email'], [0, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}