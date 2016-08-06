<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model {

	protected $table = 'subscriptions';
	
	public $timestamps = true;
	public function getDietaryPreferencesAttribute($value)
    {
    	$prefs = explode(",",$value);
		$string_pref = "";
	
		foreach ($prefs as $pref) {
	
			if ($string_pref != "") {
				$string_pref .= ", ";
			}
			if ($pref=="1") {
				$string_pref .= "Beef ";
			}
			if ($pref=="2") {
				$string_pref .= "Poultry ";
			}
			if ($pref=="3") {
				$string_pref .= "Fish ";
			}
			if ($pref=="4") {
				$string_pref .= "Lamb ";
			}
			if ($pref=="5") {
				$string_pref .= "Pork ";
			}
			
			if ($pref=="6") {
				$string_pref .= "Shellfish ";
			}
			if ($pref=="7") {
				$string_pref .= "Nuts ";
			}
			if ($pref=="8") {
				$string_pref .= "Adventurous ";
			}
			if ($pref=="9") {
				$string_pref .= "Gluten Free ";
			}
		}
		return $string_pref;
	}
}