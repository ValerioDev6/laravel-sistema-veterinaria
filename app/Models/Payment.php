<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * 
 * @property int $id
 * @property int $invoice_id
 * @property float $amount
 * @property float|null $advance_amount
 * @property string $payment_method
 * @property string|null $status
 * @property Carbon $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Invoice $invoice
 *
 * @package App\Models
 */
class Payment extends Model
{
	protected $table = 'payments';

	protected $casts = [
		'invoice_id' => 'int',
		'amount' => 'float',
		'advance_amount' => 'float',
		'paid_at' => 'datetime'
	];

	protected $fillable = [
		'invoice_id',
		'amount',
		'advance_amount',
		'payment_method',
		'status',
		'paid_at'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
}
