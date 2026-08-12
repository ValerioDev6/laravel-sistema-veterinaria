<?php

namespace App\Actions\MedicalRecords;

use App\Filters\MedicalRecords\FiltrarPorPet;
use App\Filters\Shared\FiltrarPorBusqueda;
use App\Filters\Shared\OrdenarPor;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ListMedicalRecordsAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send(
                MedicalRecord::with([
                    'paciente',
                    'user',
                    'prescriptions.medicine',
                    'vital_signs',
                    'medical_record_attachments',
                ]),
            )
            ->through([
                new FiltrarPorPet($request),
                new FiltrarPorBusqueda($request, ['event_type', 'event_date']),
                new OrdenarPor($request, [0 => 'id', 1 => 'event_date'], [0, 'desc']),
            ])
            ->thenReturn();

        return $query->paginate($request->integer('per_page', 15));
    }
}