<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan_change extends Model {

    const STATUS_TO_CHANGE = 'to_change';
    const STATUS_WAS_CHANGED = 'was_changed';
    const STATUS_INACTIVE = 'inactive';

	protected $table = 'plan_changes';
	public $timestamps = true;

}