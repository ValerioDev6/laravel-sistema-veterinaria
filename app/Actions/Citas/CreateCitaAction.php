<?php

namespace App\Actions\Citas;

use App\Models\Cita;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class CreateCitaAction
{
    public static function execute(array $data = []): Cita
    {
        return DB::transaction(function () use ($data) {
            ValidarDisponibilidadCita::execute($data);

            $serviceId = data_get($data, 'service_id');

            if (! $serviceId && ! empty($data['new_service'])) {
                $service = Service::create([
                    'name' => $data['new_service']['name'],
                    'category' => $data['new_service']['category'] ?? 'otro',
                    'base_price' => $data['new_service']['base_price'] ?? 0,
                    'duration_minutes' => $data['new_service']['duration_minutes'] ?? 30,
                    'description' => $data['new_service']['description'] ?? null,
                ]);
                $serviceId = $service->id;
            }

            $cita = Cita::create([
                'pet_id' => data_get($data, 'pet_id'),
                'veterinarian_id' => data_get($data, 'veterinarian_id'),
                'service_id' => $serviceId,
                'appointment_date' => data_get($data, 'appointment_date'),
                'appointment_time' => data_get($data, 'appointment_time'),
                'reason' => data_get($data, 'reason'),
                'reprogramming' => data_get($data, 'reprogramming', false),
                'status' => data_get($data, 'status', 'confirmada'),
                'created_by_user_id' => data_get(
                    $data,
                    'created_by_user_id',
                    auth()->id(),
                ),
            ]);

            self::registrarPago($cita, $data);
            self::sincronizarReminder($cita, $data);

            return $cita->fresh(['paciente', 'veterinarian', 'service']);
        });
    }

    private static function registrarPago(Cita $cita, array $data): void
    {
        $total = (float) ($cita->service?->base_price ?? 0);
        $advance = (float) data_get($data, 'advance_amount', 0);
        $method = data_get($data, 'payment_method', 'otro');

        $invoice = Invoice::create([
            'invoiceable_type' => 'cita',
            'invoiceable_id' => $cita->id,
            'owner_id' => $cita->paciente?->owner_id,
            'total' => $total,
            'remaining_balance' => round($total - $advance, 2),
            'status' => $advance >= $total ? 'pagado' : 'parcial',
            'issued_at' => $cita->appointment_date
                ? $cita->appointment_date->format('Y-m-d')
                : now()->toDateString(),
        ]);

        if ($advance > 0) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $advance,
                'advance_amount' => $advance,
                'payment_method' => $method,
                'status' => 'pagado',
                'paid_at' => now(),
            ]);
        }

        if ($advance >= $total && $cita->status === 'pendiente') {
            $cita->update(['status' => 'confirmada']);
        }
    }

    private static function sincronizarReminder(Cita $cita, array $data): void
    {
        $reminderDate = data_get($data, 'reminder_date');

        if (! $reminderDate) {
            return;
        }

        Reminder::updateOrCreate(
            [
                'pet_id' => $cita->pet_id,
                'remindable_type' => 'cita',
                'remindable_id' => $cita->id,
            ],
            [
                'remind_at' => \Carbon\Carbon::parse($reminderDate),
                'message' => $cita->reason
                    ? 'Cita: ' . $cita->reason
                    : 'Cita programada',
                'status' => 'pendiente',
            ],
        );
    }
}
