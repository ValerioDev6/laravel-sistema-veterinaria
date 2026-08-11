<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Surgiere
 *
 * @property int $id
 * @property int $pet_id
 * @property int $veterinarian_id
 * @property int|null $cita_id
 * @property string|null $surgery_type
 * @property Carbon $surgery_date
 * @property string|null $outcome
 * @property string|null $status
 * @property string|null $medical_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Paciente $paciente
 * @property User $user
 * @property Cita|null $cita
 * @property Collection|MedicalRecord[] $medical_records
 *
 * @package App\Models
 */
class Surgiere extends Model
{
	protected $table = 'surgiere';

	protected $casts = [
		'pet_id' => 'int',
		'veterinarian_id' => 'int',
		'cita_id' => 'int',
		'surgery_date' => 'datetime'
	];

	protected $fillable = [
		'pet_id',
		'veterinarian_id',
		'cita_id',
		'surgery_type',
		'surgery_date',
		'outcome',
		'status',
		'medical_notes'
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

	public function invoice()
	{
		return $this->hasOne(Invoice::class, 'invoiceable_id')
			->where('invoiceable_type', 'surgiere');
	}

	public function medical_records()
	{
		return $this->hasMany(MedicalRecord::class, 'surgery_id');
	}
}
