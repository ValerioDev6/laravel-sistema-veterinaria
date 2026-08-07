<?php

namespace App\Actions\MedicalRecordAttachments;

use App\Models\MedicalRecordAttachment;
use App\Support\ImageUploader;
use Illuminate\Http\UploadedFile;

class UploadAttachmentAction
{
    public function execute(int $recordId, UploadedFile $file): MedicalRecordAttachment
    {
        $folder = "medical-records/{$recordId}";
        $upload = ImageUploader::upload($file, $folder);

        $extension = $file->getClientOriginalExtension();
        $fileType = in_array(strtolower($extension), ["jpeg", "jpg", "png", "webp"]) ? "image" : "pdf";

        return MedicalRecordAttachment::create([
            "medical_record_id" => $recordId,
            "file_url" => $upload["url"],
            "file_public_id" => $upload["public_id"] ?? null,
            "file_type" => $fileType,
        ]);
    }
}