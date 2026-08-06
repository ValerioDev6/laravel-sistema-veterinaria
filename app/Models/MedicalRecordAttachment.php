<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalRecordAttachment
 * 
 * @property int $id
 * @property int $medical_record_id
 * @property string $file_url
 * @property string|null $file_public_id
 * @property string|null $file_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property MedicalRecord $medical_record
 *
 * @package App\Models
 */
class MedicalRecordAttachment extends Model
{
	protected $table = 'medical_record_attachments';

	protected $casts = [
		'medical_record_id' => 'int'
	];

	protected $fillable = [
		'medical_record_id',
		'file_url',
		'file_public_id',
		'file_type'
	];

	public function medical_record()
	{
		return $this->belongsTo(MedicalRecord::class);
	}
}
