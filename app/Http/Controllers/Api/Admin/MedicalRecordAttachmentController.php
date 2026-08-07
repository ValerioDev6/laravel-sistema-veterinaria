<?php

namespace App\Http\Controllers\Api\Admin;

use App\Actions\MedicalRecordAttachments\DeleteAttachmentAction;
use App\Actions\MedicalRecordAttachments\UploadAttachmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecordAttachments\StoreAttachmentRequest;
use App\Http\Resources\MedicalRecordAttachmentResource;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use Illuminate\Http\JsonResponse;

class MedicalRecordAttachmentController extends Controller
{
    public function store(
        StoreAttachmentRequest $request,
        MedicalRecord $medicalRecord,
        UploadAttachmentAction $action
    ): JsonResponse {
        $attachment = $action->execute($medicalRecord->id, $request->file("file"));

        return response()->json([
            "status" => true,
            "message" => "Archivo adjunto subido correctamente",
            "data" => new MedicalRecordAttachmentResource($attachment),
            "errors" => (object) [],
        ], 201);
    }

    public function destroy(
        MedicalRecordAttachment $medicalRecordAttachment,
        DeleteAttachmentAction $action
    ): JsonResponse {
        $action->execute($medicalRecordAttachment);

        return response()->json([
            "status" => true,
            "message" => "Archivo adjunto eliminado correctamente",
            "data" => (object) [],
            "errors" => (object) [],
        ]);
    }
}