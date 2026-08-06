<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property string $invoiceable_type
 * @property int $invoiceable_id
 * @property int $owner_id
 * @property float $total
 * @property float $remaining_balance
 * @property string|null $status
 * @property Carbon $issued_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Owner $owner
 * @property Collection|Payment[] $payments
 *
 * @package App\Models
 */
class Invoice extends Model
{
	protected $table = 'invoices';

	protected $casts = [
		'invoiceable_id' => 'int',
		'owner_id' => 'int',
		'total' => 'float',
		'remaining_balance' => 'float',
		'issued_at' => 'datetime'
	];

	protected $fillable = [
		'invoiceable_type',
		'invoiceable_id',
		'owner_id',
		'total',
		'remaining_balance',
		'status',
		'issued_at'
	];

	public function owner()
	{
		return $this->belongsTo(Owner::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}
}
