<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalRecord
 *
 * @property int $id
 * @property int $pet_id
 * @property int $veterinarian_id
 * @property int|null $cita_id
 * @property int|null $vaccination_id
 * @property int|null $surgery_id
 * @property string $event_type
 * @property Carbon $event_date
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Paciente $paciente
 * @property User $user
 * @property Cita|null $cita
 * @property Vacuna|null $vacuna
 * @property Surgiere|null $surgiere
 * @property Collection|MedicalRecordAttachment[] $medical_record_attachments
 * @property Collection|Prescription[] $prescriptions
 * @property Collection|VitalSign[] $vital_signs
 *
 * @package App\Models
 */
class MedicalRecord extends Model
{
	protected $table = 'medical_record';

	protected $casts = [
		'pet_id' => 'int',
		'veterinarian_id' => 'int',
		'cita_id' => 'int',
		'vaccination_id' => 'int',
		'surgery_id' => 'int',
		'event_date' => 'datetime'
	];

	protected $fillable = [
		'pet_id',
		'veterinarian_id',
		'cita_id',
		'vaccination_id',
		'surgery_id',
		'event_type',
		'event_date',
		'notes'
	];

	public function paciente()
	{
		return $this->belongsTo(Paciente::class, 'pet_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'veterinarian_id');
	}

	public function cita()
	{
		return $this->belongsTo(Cita::class);
	}

	public function vacuna()
	{
		return $this->belongsTo(Vacuna::class, 'vaccination_id');
	}

	public function surgiere()
	{
		return $this->belongsTo(Surgiere::class, 'surgery_id');
	}

	public function medical_record_attachments()
	{
		return $this->hasMany(MedicalRecordAttachment::class);
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}

	public function vital_signs()
	{
		return $this->hasMany(VitalSign::class);
	}
}
