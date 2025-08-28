<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
	use HasFactory;

	protected $guarded = [];

	protected $casts = [
		'config' => 'array',
	];

	public function messages(): HasMany
	{
		return $this->hasMany(Message::class);
	}
}
