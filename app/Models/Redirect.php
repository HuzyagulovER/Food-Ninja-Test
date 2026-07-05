<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int link_id
 */
class Redirect
	extends Model
{
	protected $fillable = [
		'link_id',
		'ip_address',
	];

	public function getUpdatedAtColumn(): null
	{
		return null;
	}

	public function link(): BelongsTo
	{
		return $this->belongsTo(Link::class);
	}
}
