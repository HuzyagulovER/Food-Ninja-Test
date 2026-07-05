<?php

namespace App\Models;

use App\Observers\LinkObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(LinkObserver::class)]
class Link
	extends Model
{
	protected $fillable = [
		'user_id',
		'title',
		'link',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function redirects(): HasMany
	{
		return $this->hasMany(Redirect::class);
	}
}
