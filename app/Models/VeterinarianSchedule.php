<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VeterinarianSchedule
 * 
 * @property int $id
 * @property int $veterinarian_id
 * @property int $day_of_week
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property bool|null $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class VeterinarianSchedule extends Model
{
	protected $table = 'veterinarian_schedules';

	protected $casts = [
		'veterinarian_id' => 'int',
		'day_of_week' => 'int',
		'start_time' => 'datetime',
		'end_time' => 'datetime',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'veterinarian_id',
		'day_of_week',
		'start_time',
		'end_time',
		'is_active'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'veterinarian_id');
	}

	public function getDayLabelAttribute(): string
	{
		return match ((int) $this->day_of_week) {
			0 => 'Domingo',
			1 => 'Lunes',
			2 => 'Martes',
			3 => 'Miércoles',
			4 => 'Jueves',
			5 => 'Viernes',
			6 => 'Sábado',
			default => 'Desconocido',
		};
	}
}
