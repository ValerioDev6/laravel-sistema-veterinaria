<?php

namespace App\Actions\MedicalRecordAttachments;

use App\Models\MedicalRecordAttachment;
use App\Support\ImageUploader;

class DeleteAttachmentAction
{
    public function execute(MedicalRecordAttachment $attachment): void
    {
        if (!empty($attachment->file_public_id)) {
            ImageUploader::delete($attachment->file_public_id);
        }

        $attachment->delete();
    }
}