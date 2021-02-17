<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

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

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

	public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_action_logs');
    }
}