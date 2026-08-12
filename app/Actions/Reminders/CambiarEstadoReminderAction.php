<?php

namespace App\Actions\Reminders;

use App\Models\Reminder;

class CambiarEstadoReminderAction
{
    public function execute(Reminder $reminder, string $status): Reminder
    {
        $reminder->update(["status" => $status]);

        return $reminder->fresh();
    }
}