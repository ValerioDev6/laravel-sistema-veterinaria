<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $category
 * @property float $base_price
 * @property int|null $duration_minutes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Cita[] $citas
 *
 * @package App\Models
 */
class Service extends Model
{
	protected $table = 'services';

	protected $casts = [
		'base_price' => 'float',
		'duration_minutes' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'category',
		'base_price',
		'duration_minutes'
	];

	public function citas()
	{
		return $this->hasMany(Cita::class);
	}
}
