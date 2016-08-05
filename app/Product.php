<?php

namespace App;
use stdClass;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	protected $table = 'products';
	public $timestamps = true;

	//split the sku into a string
	public function productDetails() {
		$sku = str_split($this->attributes['sku'],2);
		$productInformation = new stdClass;
		if ($sku[0]=="01"){
			$productInformation->BoxType = "Vegetarian";
			$productInformation->BoxSelectVeg = "true";
			$productInformation->BoxSelectOmn = "false";
		}
		if ($sku[0]=="02"){
			$productInformation->BoxType = "Omnivore";
			$productInformation->BoxSelectVeg = "false";
			$productInformation->BoxSelectOmn = "true";
		}
	
		if ($sku[2]=="00"){
			$productInformation->PlanType = "Adult Plan";
			$productInformation->PlanTypeSelect = "adult";
			$productInformation->FamilySize = "2 Adults";
			$productInformation->ChildSelect = 0;
		}else{
			$productInformation->PlanType = "Family";
			$productInformation->PlanTypeSelect = "family";
			$productInformation->FamilySize = "2 Adults,"." ".(integer)$sku[2] . " Children";
			$productInformation->ChildSelect = (integer)$sku[2];
		}
		return $productInformation;
	}
	
	public function subscriptions()
    {
        return $this->hasMany('App\UserSubscription');
    }
	
	
}