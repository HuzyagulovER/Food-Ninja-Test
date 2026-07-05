<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
