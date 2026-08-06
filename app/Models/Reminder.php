<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reminder
 * 
 * @property int $id
 * @property int $pet_id
 * @property string $remindable_type
 * @property int $remindable_id
 * @property Carbon $remind_at
 * @property string|null $message
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Paciente $paciente
 *
 * @package App\Models
 */
class Reminder extends Model
{
	protected $table = 'reminders';

	protected $casts = [
		'pet_id' => 'int',
		'remindable_id' => 'int',
		'remind_at' => 'datetime'
	];

	protected $fillable = [
		'pet_id',
		'remindable_type',
		'remindable_id',
		'remind_at',
		'message',
		'status'
	];

	public function paciente()
	{
		return $this->belongsTo(Paciente::class, 'pet_id');
	}
}
