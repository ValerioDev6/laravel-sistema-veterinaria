<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cita
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $veterinarian_id
 * @property int|null $service_id
 * @property int|null $created_by_user_id
 * @property Carbon $appointment_date
 * @property Carbon $appointment_time
 * @property string|null $reason
 * @property bool|null $reprogramming
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Paciente $paciente
 * @property User|null $user
 * @property Service|null $service
 * @property Collection|MedicalRecord[] $medical_records
 * @property Collection|Surgiere[] $surgieres
 * @property Collection|Vacuna[] $vacunas
 *
 * @package App\Models
 */
class Cita extends Model
{
	protected $table = 'citas';

	protected $casts = [
		'pet_id' => 'int',
		'veterinarian_id' => 'int',
		'service_id' => 'int',
		'created_by_user_id' => 'int',
		'appointment_date' => 'datetime',
		'appointment_time' => 'datetime',
		'reprogramming' => 'bool'
	];

	protected $fillable = [
		'pet_id',
		'veterinarian_id',
		'service_id',
		'created_by_user_id',
		'appointment_date',
		'appointment_time',
		'reason',
		'reprogramming',
		'status'
	];

	public function paciente()
	{
		return $this->belongsTo(Paciente::class, 'pet_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by_user_id');
	}

	public function service()
	{
		return $this->belongsTo(Service::class);
	}

	public function medical_records()
	{
		return $this->hasMany(MedicalRecord::class);
	}

	public function surgieres()
	{
		return $this->hasMany(Surgiere::class);
	}

	public function vacunas()
	{
		return $this->hasMany(Vacuna::class);
	}
}
