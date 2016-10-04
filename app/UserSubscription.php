<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model {

	protected $table = 'subscriptions';
	
	public $timestamps = true;
	
	public function getNutFreeOrGlutenFree() {
		
		$prefs = explode(",",$this->dietary_preferences);
		$string_pref = "";
	
		foreach ($prefs as $pref) {
		
			if (trim($pref)=="Nut Free") {
				if ($string_pref != "") {
					$string_pref .= ", ";
				}
				$string_pref .= "Nut Free ";
			}
		
			if (trim($pref)=="Gluten Free") {
				if ($string_pref != "") {
					$string_pref .= ", ";
				}
				$string_pref .= "Gluten Free ";
			}
		}
		return $string_pref;
		
	}
	public function getDietaryPreferencesAttribute($value)
    {
    	$prefs = explode(",",$value);
		$string_pref = "";
	
		foreach ($prefs as $pref) {
	
			if ($string_pref != "") {
				$string_pref .= ", ";
			}
			if ($pref=="1") {
				$string_pref .= "Red Meat ";
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
				$string_pref .= "Nut Free ";
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
	
	public function product()
    {
        return $this->belongsTo('App\Product');
    }

	
}