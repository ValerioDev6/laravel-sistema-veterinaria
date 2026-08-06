<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * 
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string|null $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Branch extends Model
{
	protected $table = 'branches';

	protected $fillable = [
		'name',
		'address',
		'city',
		'phone'
	];

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
