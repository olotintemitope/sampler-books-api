<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserActionLog extends Pivot
{
	protected $table = 'user_action_logs';

	/**
	 * The attributes that are mass assignable.
	 * 
	 * @var array
	 */
	protected $fillable = [
		'book_id',
		'user_id',
		'action',
	];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}