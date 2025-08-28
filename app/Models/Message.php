<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
	use HasFactory;

	protected $guarded = [];

	protected $casts = [
		'meta' => 'array',
		'sent_at' => 'datetime',
	];

	public function contact(): BelongsTo
	{
		return $this->belongsTo(Contact::class);
	}

	public function channel(): BelongsTo
	{
		return $this->belongsTo(Channel::class);
	}
}
