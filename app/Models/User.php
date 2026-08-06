<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $phone
 * @property string|null $type_documento
 * @property string|null $n_documento
 * @property Carbon|null $birthday
 * @property string|null $avatar
 * @property string|null $avatar_public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Branch|null $branch
 * @property Collection|Cita[] $citas
 * @property Collection|MedicalRecord[] $medical_records
 * @property Collection|Surgiere[] $surgieres
 * @property Collection|Vacuna[] $vacunas
 * @property Collection|VeterinarianSchedule[] $veterinarian_schedules
 *
 * @package App\Models
 */
class User extends Model
{
	use HasFactory, HasRoles;

	protected $table = 'users';

	protected $casts = [
		'branch_id' => 'int',
		'email_verified_at' => 'datetime',
		'birthday' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'branch_id',
		'username',
		'email',
		'email_verified_at',
		'password',
		'remember_token',
		'phone',
		'type_documento',
		'n_documento',
		'birthday',
		'avatar',
		'avatar_public_id'
	];

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function citas()
	{
		return $this->hasMany(Cita::class, 'created_by_user_id');
	}

	public function medical_records()
	{
		return $this->hasMany(MedicalRecord::class, 'veterinarian_id');
	}

	public function surgieres()
	{
		return $this->hasMany(Surgiere::class, 'veterinarian_id');
	}

	public function vacunas()
	{
		return $this->hasMany(Vacuna::class, 'veterinarian_id');
	}

	public function veterinarian_schedules()
	{
		return $this->hasMany(VeterinarianSchedule::class, 'veterinarian_id');
	}
}
