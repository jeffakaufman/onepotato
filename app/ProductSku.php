<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 14/10/16
 * Time: 10:29
 */

namespace App;


class ProductSku {

    /**
     * @param $sku
     * @return ProductSku|bool
     */
    public static function BuildByText($sku) {
        $sku_array = str_split($sku, 2);
//var_dump($sku_array);
        if(5 != count($sku_array)) return null;

        $obj = new self();

        switch($sku_array[0]) {
            case '02':
                $obj->SetPlanType(self::PLAN_TYPE_OMNIVORE);
                break;

            case '01':
            default:
                $obj->SetPlanType(self::PLAN_TYPE_VEGETARIAN);
                break;
        }

        $obj->SetNumChildren((int)$sku_array[2]);

        if($sku_array[3] == '01') {
            $obj->SetGlutenFree(true);
        }

        return $obj;

    }

    public function IsEqualAs(ProductSku $compareWith) {
        return $this->GetAsString() == $compareWith->GetAsString();
    }

    public function __construct() {
        $this->numAdults = 2;
        $this->numChildren = 0;
        $this->glutenFree = false;
    }


    const PLAN_TYPE_OMNIVORE = 'omni';
    const PLAN_TYPE_VEGETARIAN = 'veg';

    const NUM_CHILDREN_MAX = 4;

    private $planType;

    private $numAdults = 2; // For now there is only ability to have 2 here, no way to change should be allowed

    private $numChildren = 0;

    private $glutenFree = false;

    public function SetPlanType($planType) {
        if(in_array($planType, array(self::PLAN_TYPE_OMNIVORE, self::PLAN_TYPE_VEGETARIAN))) {
            $this->planType;
        }

        return $this;
    }

    public function SetNumChildren($numChildren) {
        $numChildren = (int)$numChildren;
        if(0 > $numChildren) {
            $numChildren = 0;
        }

        if(self::NUM_CHILDREN_MAX < $numChildren) {
            $numChildren = self::NUM_CHILDREN_MAX;
        }

        $this->numChildren = $numChildren;

        return $this;
    }

    public function SetGlutenFree($gf) {
        $this->glutenFree = (bool)$gf;
    }


    public function GetAsString() {
        return $this->__toString();
    }

    public function GetNumAdults() {
        return $this->numAdults;
    }

    public function GetNumChildren() {
        return $this->numChildren;
    }

    public function IsGlutenFree() {
        return (bool)$this->glutenFree;
    }


    public function __toString() {
        $sku = '';
        $sku .= $this->_getPlanTypePart();
        $sku .= $this->_getNumAdultsPart();
        $sku .= $this->_getNumChildrenPart();
        $sku .= $this->_getGFPart();
        $sku .= $this->_getUnusedPart();

        return $sku;
    }

    private function _getNumAdultsPart() {
        return "0".((int)$this->numAdults);
    }

    private function _getNumChildrenPart() {
        return "0".((int)$this->numChildren);
    }

    private function _getPlanTypePart() {
        $part = '00';
        switch($this->planType) {
            case self::PLAN_TYPE_VEGETARIAN:
                $part = '01';
                break;

            case self::PLAN_TYPE_OMNIVORE:
                $part = '02';
                break;
        }
        return $part;
    }

    private function _getGFPart() {
        $part = '00';
        if($this->glutenFree) {
            $part = '01';
        }
        return $part;
    }

    private function _getUnusedPart() {
        return '00';
    }
}

/*
01  veg/onmivore
02	num adults
03	num children (04= 4 children)
01  Gluten Free
00	unused
*/
