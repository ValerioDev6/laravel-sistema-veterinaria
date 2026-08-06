<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prescription
 * 
 * @property int $id
 * @property int $medical_record_id
 * @property int $medicine_id
 * @property string $dosage
 * @property int|null $duration_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MedicalRecord $medical_record
 * @property Medicine $medicine
 *
 * @package App\Models
 */
class Prescription extends Model
{
	protected $table = 'prescriptions';

	protected $casts = [
		'medical_record_id' => 'int',
		'medicine_id' => 'int',
		'duration_days' => 'int'
	];

	protected $fillable = [
		'medical_record_id',
		'medicine_id',
		'dosage',
		'duration_days'
	];

	public function medical_record()
	{
		return $this->belongsTo(MedicalRecord::class);
	}

	public function medicine()
	{
		return $this->belongsTo(Medicine::class);
	}
}
