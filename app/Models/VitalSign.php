<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VitalSign
 * 
 * @property int $id
 * @property int $pet_id
 * @property int|null $medical_record_id
 * @property float|null $weight
 * @property float|null $temperature
 * @property int|null $heart_rate
 * @property Carbon $recorded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Paciente $paciente
 * @property MedicalRecord|null $medical_record
 *
 * @package App\Models
 */
class VitalSign extends Model
{
	protected $table = 'vital_signs';

	protected $casts = [
		'pet_id' => 'int',
		'medical_record_id' => 'int',
		'weight' => 'float',
		'temperature' => 'float',
		'heart_rate' => 'int',
		'recorded_at' => 'datetime'
	];

	protected $fillable = [
		'pet_id',
		'medical_record_id',
		'weight',
		'temperature',
		'heart_rate',
		'recorded_at'
	];

	public function paciente()
	{
		return $this->belongsTo(Paciente::class, 'pet_id');
	}

	public function medical_record()
	{
		return $this->belongsTo(MedicalRecord::class);
	}
}
