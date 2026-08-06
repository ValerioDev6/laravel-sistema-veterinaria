<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vacuna
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $veterinarian_id
 * @property int $vaccine_type_id
 * @property int|null $cita_id
 * @property Carbon $vaccination_date
 * @property Carbon|null $next_due_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Paciente $paciente
 * @property User $user
 * @property VaccineType $vaccine_type
 * @property Cita|null $cita
 * @property Collection|MedicalRecord[] $medical_records
 *
 * @package App\Models
 */
class Vacuna extends Model
{
	protected $table = 'vacunas';

	protected $casts = [
		'pet_id' => 'int',
		'veterinarian_id' => 'int',
		'vaccine_type_id' => 'int',
		'cita_id' => 'int',
		'vaccination_date' => 'datetime',
		'next_due_date' => 'datetime'
	];

	protected $fillable = [
		'pet_id',
		'veterinarian_id',
		'vaccine_type_id',
		'cita_id',
		'vaccination_date',
		'next_due_date'
	];

	public function paciente()
	{
		return $this->belongsTo(Paciente::class, 'pet_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'veterinarian_id');
	}

	public function vaccine_type()
	{
		return $this->belongsTo(VaccineType::class);
	}

	public function cita()
	{
		return $this->belongsTo(Cita::class);
	}

	public function medical_records()
	{
		return $this->hasMany(MedicalRecord::class, 'vaccination_id');
	}
}
