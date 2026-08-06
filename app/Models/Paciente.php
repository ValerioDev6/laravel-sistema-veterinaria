<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Paciente
 * 
 * @property int $id
 * @property int $owner_id
 * @property int $species_id
 * @property int|null $breed_id
 * @property string $name
 * @property Carbon|null $birth_date
 * @property string|null $gender
 * @property string|null $color
 * @property float|null $weight
 * @property string|null $photo
 * @property string|null $photo_public_id
 * @property string|null $medical_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Owner $owner
 * @property Species $species
 * @property Breed|null $breed
 * @property Collection|Cita[] $citas
 * @property Collection|MedicalRecord[] $medical_records
 * @property Collection|Reminder[] $reminders
 * @property Collection|Surgiere[] $surgieres
 * @property Collection|Vacuna[] $vacunas
 * @property Collection|VitalSign[] $vital_signs
 *
 * @package App\Models
 */
class Paciente extends Model
{
	protected $table = 'pacientes';

	protected $casts = [
		'owner_id' => 'int',
		'species_id' => 'int',
		'breed_id' => 'int',
		'birth_date' => 'datetime',
		'weight' => 'float'
	];

	protected $fillable = [
		'owner_id',
		'species_id',
		'breed_id',
		'name',
		'birth_date',
		'gender',
		'color',
		'weight',
		'photo',
		'photo_public_id',
		'medical_notes'
	];

	public function owner()
	{
		return $this->belongsTo(Owner::class);
	}

	public function species()
	{
		return $this->belongsTo(Species::class);
	}

	public function breed()
	{
		return $this->belongsTo(Breed::class);
	}

	public function citas()
	{
		return $this->hasMany(Cita::class, 'pet_id');
	}

	public function medical_records()
	{
		return $this->hasMany(MedicalRecord::class, 'pet_id');
	}

	public function reminders()
	{
		return $this->hasMany(Reminder::class, 'pet_id');
	}

	public function surgieres()
	{
		return $this->hasMany(Surgiere::class, 'pet_id');
	}

	public function vacunas()
	{
		return $this->hasMany(Vacuna::class, 'pet_id');
	}

	public function vital_signs()
	{
		return $this->hasMany(VitalSign::class, 'pet_id');
	}
}
