<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Medicine
 * 
 * @property int $id
 * @property string $name
 * @property int $quantity
 * @property float $unit_cost
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Prescription[] $prescriptions
 *
 * @package App\Models
 */
class Medicine extends Model
{
	protected $table = 'medicines';

	protected $casts = [
		'quantity' => 'int',
		'unit_cost' => 'float'
	];

	protected $fillable = [
		'name',
		'quantity',
		'unit_cost'
	];

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}
}
