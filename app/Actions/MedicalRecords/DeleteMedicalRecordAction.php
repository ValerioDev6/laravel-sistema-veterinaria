<?php

namespace App\Actions\MedicalRecords;

use App\Models\MedicalRecord;

class DeleteMedicalRecordAction
{
    public function execute(MedicalRecord $record): void
    {
        $record->delete();
    }
}