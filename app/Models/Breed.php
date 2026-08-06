<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Breed
 * 
 * @property int $id
 * @property int $species_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Species $species
 * @property Collection|Paciente[] $pacientes
 *
 * @package App\Models
 */
class Breed extends Model
{
	protected $table = 'breeds';

	protected $casts = [
		'species_id' => 'int'
	];

	protected $fillable = [
		'species_id',
		'name'
	];

	public function species()
	{
		return $this->belongsTo(Species::class);
	}

	public function pacientes()
	{
		return $this->hasMany(Paciente::class);
	}
}
