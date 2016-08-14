<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dietary_preference extends Model {

// 	protected $table = 'dietary_preferences';
// 	public $timestamps = true;

// }

	protected $table = 'subscriptions';
	
	public $timestamps = true;
	public function getDietaryPreferences($value)
    {
		return $value;
	}
}