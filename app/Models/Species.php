<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Species
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Breed[] $breeds
 * @property Collection|Paciente[] $pacientes
 * @property Collection|VaccineType[] $vaccine_types
 *
 * @package App\Models
 */
class Species extends Model
{
	protected $table = 'species';

	protected $fillable = [
		'name'
	];

	public function breeds()
	{
		return $this->hasMany(Breed::class);
	}

	public function pacientes()
	{
		return $this->hasMany(Paciente::class);
	}

	public function vaccine_types()
	{
		return $this->hasMany(VaccineType::class);
	}
}
