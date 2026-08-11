<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VaccineType
 * 
 * @property int $id
 * @property string $name
 * @property float $base_price
 * @property int|null $species_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Species|null $species
 * @property Collection|Vacuna[] $vacunas
 *
 * @package App\Models
 */
class VaccineType extends Model
{
	protected $table = 'vaccine_types';

	protected $casts = [
		'species_id' => 'int',
		'base_price' => 'float'
	];

	protected $fillable = [
		'name',
		'base_price',
		'species_id'
	];

	public function species()
	{
		return $this->belongsTo(Species::class);
	}

	public function vacunas()
	{
		return $this->hasMany(Vacuna::class);
	}
}
