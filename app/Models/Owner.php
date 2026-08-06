<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Owner
 * 
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $type_documento
 * @property string|null $n_documento
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Invoice[] $invoices
 * @property Collection|Paciente[] $pacientes
 *
 * @package App\Models
 */
class Owner extends Model
{
	protected $table = 'owners';

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'phone',
		'address',
		'city',
		'type_documento',
		'n_documento'
	];

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function pacientes()
	{
		return $this->hasMany(Paciente::class);
	}
}
