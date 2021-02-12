<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
	protected $table = 'books';

	/**
	 * The attributes that are mass assignable.
	 * 
	 * @var array
	 */
	protected $fillable = [
		'title',
		'isbn',
		'published_at',
		'status',
	];

	/**
	 * @var array
	 */
	protected $casts = [
		'published_at' => 'date',
	];

	public function userActionLogs()
    {
        return $this->hasMany(UserActionLog::class);
    }
}