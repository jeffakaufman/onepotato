<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 13/09/16
 * Time: 20:18
 */

namespace App;

use DB;


class MenuAssigner {

    public function __construct(\DateTime $deliveryDate) {
        $this->deliveryDate = $deliveryDate;
        $this->weekMenuList = $this->_getWeekMenuList($this->_getCurrentMenus($deliveryDate));
    }


    public function GetUserMenus(User $user) {
        $weekMenuList = $this->weekMenuList;

        $subscription = UserSubscription::where('user_id', $user->id)->first();
        if(!$subscription) throw new \Exception("No subscription", 10);
        /**
         * @var UserSubscription $subscription
         */


        $product = Product::find($subscription->product_id);
        if(!$product) throw new \Exception("No product selected", 11);
        /**
         * @var Product $product
         */


        $menusToAssign = [];

        if($weekMenuList->primaryMenu) {
            $menusToAssign[] = $weekMenuList->primaryMenu;
        }

        if($product->IsVegetarian()) {
            if($weekMenuList->vegMenu1) {
                $menusToAssign[] = $weekMenuList->vegMenu1;
            }
            if($weekMenuList->vegMenu2) {
                $menusToAssign[] = $weekMenuList->vegMenu2;
            }
        } else { // Omnivore
            $replacementVegUsed = false;

            $sMask = $this->_createSubscriptionMask($subscription->getAttributes()['dietary_preferences']);

            //Process First Omnivore Meal
            if($weekMenuList->omnMenu1) {
                $_replacementReason = ($sMask & $weekMenuList->omnMenu1->mask) ^ $weekMenuList->omnMenu1->mask;
                $_replacementIsNecessary = (bool)$_replacementReason;

                if($_replacementIsNecessary) {
//echo "Replacing {$_replacementReason}\r\n";
//printf("%06b \r\n%06b \r\n%06b \r\n%06b \r\n\r\n", $sMask, $this->omnMenu1->mask, $sMask & $this->omnMenu1->mask, ($sMask & $this->omnMenu1->mask) ^ $this->omnMenu1->mask);
                    if($replacementVegUsed) {
                        if($weekMenuList->vegMenu2) {
                            $menusToAssign[] = $weekMenuList->vegMenu2;
                        }
                    } else {
                        if($weekMenuList->vegMenu1) {
                            $menusToAssign[] = $weekMenuList->vegMenu1;
                            $replacementVegUsed = true;
                        }
                    }
                } else {
                    $menusToAssign[] = $weekMenuList->omnMenu1;
                }
            }

            //Process Second Omnivore Meal
            if($weekMenuList->omnMenu2) {
                $_replacementReason = ($sMask & $weekMenuList->omnMenu2->mask) ^ $weekMenuList->omnMenu2->mask;
                $_replacementIsNecessary = (bool)$_replacementReason;

                if($_replacementIsNecessary) {
//echo "Replacing {$_replacementReason}\r\n";
//printf("%06b \r\n%06b \r\n%06b \r\n%06b \r\n\r\n", $sMask, $this->omnMenu2->mask, $sMask & $this->omnMenu2->mask, ($sMask & $this->omnMenu2->mask) ^ $this->omnMenu2->mask);

                    if($replacementVegUsed) {
                        if($weekMenuList->vegMenu2) {
                            $menusToAssign[] = $weekMenuList->vegMenu2;
                        }
                    } else {
                        if($weekMenuList->vegMenu1) {
                            $menusToAssign[] = $weekMenuList->vegMenu1;
                            $replacementVegUsed = true;
                        }
                    }
                } else {
                    $menusToAssign[] = $weekMenuList->omnMenu2;
                }
            }
        }


        return $menusToAssign;
    }


    private function _getCurrentMenus($nextDeliveryDate) {

        $menus = DB::table('menus')
            ->join('menus_whats_cookings', 'menus.id', '=', 'menus_whats_cookings.menus_id')
            ->join('whats_cookings', 'whats_cookings.id', '=', 'menus_whats_cookings.whats_cookings_id')
            ->where('whats_cookings.week_of', '=', $nextDeliveryDate)
            ->get(['menus.*']);

        return $menus;
    }

    private function _getWeekMenuList($menus) {

//var_dump($menus);die();

        $weekMenuList = new \stdClass();
        $weekMenuList->primaryMenu = null;
        $weekMenuList->vegMenu1 = null;
        $weekMenuList->vegMenu2 = null;
        $weekMenuList->omnMenu1 = null;
        $weekMenuList->omnMenu2 = null;

        foreach($menus as $m) {
            $m->_marked = false;
        }

        // Find Primary Menu
        foreach($menus as $m) {
            if($m->isVegetarian && $m->isOmnivore) {
                $weekMenuList->primaryMenu = $m;
                $m->_marked = true;
                break;
            }
        }

//echo count($menus);
        // Find Reserve Vegetarian Menu
        foreach($menus as $m) {
            if($m->_marked) continue;
            if($m->isVegetarian && $m->vegetarianBackup) {
                $weekMenuList->vegMenu1 = $m;
                $m->_marked = true;
                break;
            }
        }
        if(!$weekMenuList->vegMenu1) {
            foreach($menus as $m) {
                if($m->_marked) continue;
                if($m->isVegetarian) {
                    $weekMenuList->vegMenu1 = $m;
                    $m->_marked = true;
                    break;
                }
            }
        }
//echo count($menus);

        // Find Second Vegetarian Menu
        foreach($menus as $m) {
            if($m->_marked) continue;
            if($m->isVegetarian) {
                $weekMenuList->vegMenu2 = $m;
                $m->_marked = true;
                break;
            }
        }
//echo count($menus);

        //Find Primary Omnivore Menu
        foreach($menus as $m) {
            if($m->_marked) continue;
            if($m->isOmnivore) {
                $m->mask = $this->_createMenuMask($m);
                $weekMenuList->omnMenu1 = $m;
                $m->_marked = true;
                break;
            }
        }
//echo count($menus);
//var_dump($menus);die();


        //Find Secondary Omnivore Menu
        foreach($menus as $m) {
            if($m->_marked) continue;
            if($m->isOmnivore) {
                $m->mask = $this->_createMenuMask($m);
                $weekMenuList->omnMenu2 = $m;
                $m->_marked = true;
                break;
            }
        }
//echo count($menus);

//        var_dump($this->omnMenu1);
//        var_dump($this->omnMenu2);

        return $weekMenuList;

    }

    private function _createMenuMask($m) {
        $mask = 0;

        if($m->hasBeef)         $mask += 1;
        if($m->hasPoultry)      $mask += 2;
        if($m->hasFish)         $mask += 4;
        if($m->hasLamb)         $mask += 8;
        if($m->hasPork)         $mask += 16;
        if($m->hasShellfish)    $mask += 32;

        return $mask;
    }

    private function _createSubscriptionMask($raw) {
        $exploded = explode(',', $raw);

        $mask = 0;
        if(in_array('1', $exploded))        $mask += 1;
        if(in_array('2', $exploded))        $mask += 2;
        if(in_array('3', $exploded))        $mask += 4;
        if(in_array('4', $exploded))        $mask += 8;
        if(in_array('5', $exploded))        $mask += 16;
        if(in_array('6', $exploded))        $mask += 32;
        return $mask;
    }



    private $weekMenuList;


    /**
     * @var \DateTime
     */
    private $deliveryDate;
}