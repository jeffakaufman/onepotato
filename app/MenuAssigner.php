<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 13/09/16
 * Time: 20:18
 */

namespace App;

use App\Console\Commands\AssignMenus;
use DB;


class MenuAssigner {

    public static function ReassignAllForDate(\DateTime $deliveryDate) {

        $menuAssigner = new self($deliveryDate);

        $insertArray = [];
        $deliveryDateText = $deliveryDate->format('Y-m-d');

        User::where('password', '<>', '')->chunk(20, function($users) use($menuAssigner, $deliveryDateText, &$insertArray) {
            foreach($users as $user) {
                /**
                 * @var User $user
                 */

//                $subscription = UserSubscription::where('user_id', $user->id)->first();
//                if(!$subscription) {
//                    echo "NO SUBSCRIPTION\r\n";
//                    continue;
//                }
//                /**
//                 * @var UserSubscription $subscription
//                 */


//                $product = Product::find($subscription->product_id);
//                if(!$product) {
//                    echo "NO PRODUCT\r\n";
//                    continue;
//                };
//                /**
//                 * @var Product $product
//                 */

                try {
                    $menusToAssign = $menuAssigner->GetUserMenus($user);
                    foreach($menusToAssign as $m) {
                        $insertArray[] = [
                            'menus_id' => $m->id,
                            'users_id' => $user->id,
                            'delivery_date' => $deliveryDateText,
                        ];
                    }

                } catch (\Exception $e) {
                    continue;
                }




//echo $user->id;
//echo ' ';
//echo $product->IsVegetarian() ? 'V' : 'O';
//echo ' ';
//foreach($menusToAssign as $m) {
//    echo $m->isVegetarian ? "V({$m->id})" : "O({$m->id})";
//}

//$current = MenusUsers::where('users_id', $user->id)->where('delivery_date', '=', $nextDeliveryDate)->get();
//foreach($current as $ass) {
//    echo $ass->menus_id.' ';
//}

//echo "\r\n";

            }
        });

        DB::table('menus_users')->where('delivery_date', '=', $deliveryDateText)->delete();
        DB::table("menus_users")->insert($insertArray);

    }

    public function __construct(\DateTime $deliveryDate) {
        $this->deliveryDate = $deliveryDate;

        $this->strategy = MenuAssignerStrategy::GetStrategy($this->_getCurrentMenus($deliveryDate));
    }


    public static function GetAllFutureMenusForUser(User $user, $from = 'now') {
        $fromDate = new \DateTime($from);

        if($user->start_date) {
            $startDate = new \DateTime($user->start_date);
            if($startDate > $fromDate) {
                $fromDate = $startDate;
            }
        }

        $wcList = WhatsCookings::where('week_of', '>=', $fromDate->format('Y-m-d'))
            ->orderBy('week_of', 'asc')
            ->get(['week_of']);
        $dates = [];
        foreach($wcList as $wc) {
            $dates[] = $wc->week_of;
        }


        $allDates = [];
        foreach($dates as $date) {
            $assigner = new self(new \DateTime($date));
            $allDates[$date] = $assigner->GetUserMenus($user);
        }

        return $allDates;
    }

    public function GetUserMenus(User $user) {
        return $this->strategy->GetUserMenus($user);
    }


    private function _getCurrentMenus($nextDeliveryDate) {

        $menus = DB::table('menus')
            ->join('menus_whats_cookings', 'menus.id', '=', 'menus_whats_cookings.menus_id')
            ->join('whats_cookings', 'whats_cookings.id', '=', 'menus_whats_cookings.whats_cookings_id')
            ->where('whats_cookings.week_of', '=', $nextDeliveryDate)
            ->get(['menus.*']);

        return $menus;
    }


    /**
     * @var MenuAssignerStrategy
     */
    private $strategy;

    /**
     * @var \DateTime
     */
    private $deliveryDate;
}


abstract class MenuAssignerStrategy {

    const STRATEGY_DUMMY = 'dummy';
    const STRATEGY_NORMAL = 'normal';
    const STRATEGY_EXTENDED = 'extended';

    /**
     * @param $weekMenus
     * @return MenuAssignerStrategy
     */
    public static function GetStrategy($weekMenus) {
        $strategyCode = self::_determineStrategy($weekMenus);
//var_dump($strategyCode);
//die();

//        $strategyCode = self::STRATEGY_EXTENDED; // TODO:: Debug - remove after release

        switch ($strategyCode) {
            case self::STRATEGY_EXTENDED:
                return new MenuAssignerStrategyExtended($weekMenus);
                break;

            case self::STRATEGY_NORMAL:
                return new MenuAssignerStrategyNormal($weekMenus);
                break;

            case self::STRATEGY_DUMMY:
            default:
                return new MenuAssignerStrategyDummy($weekMenus);
                break;
        }
    }


    private static function _determineStrategy($weekMenus) {
        $maskList = [
            'b' => 0,
            'v' => 0,
            'o' => 0,
            'vs' => 0,
            'os' => 0,
            't' => 0,
        ];

        foreach($weekMenus as $_m) {
            if($_m->isVegetarian && $_m->isOmnivore) {
                ++$maskList['b'];
                ++$maskList['v'];
                ++$maskList['o'];
                ++$maskList['t'];
            } elseif ($_m->isVegetarian) {
                ++$maskList['v'];
                ++$maskList['vs'];
                ++$maskList['t'];
            } elseif($_m->isOmnivore) {
                ++$maskList['o'];
                ++$maskList['os'];
                ++$maskList['t'];
            }
        }

        if(3 > $maskList['v']) {
            $code = self::STRATEGY_DUMMY;
        } elseif (
            (1 <= $maskList['b'])
            && (3 <= $maskList['v'])
            && (3 <= $maskList['o'])
        ) {
            $code = self::STRATEGY_NORMAL;
        } else {
            $code = self::STRATEGY_EXTENDED;
        }

        return $code;
    }

    abstract public function GetUserMenus(User $user);


    protected function _createSubscriptionMask($raw) {
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

    protected function _createMenuMask($m) {
        $mask = 0;

        if($m->hasBeef)         $mask += 1;
        if($m->hasPoultry)      $mask += 2;
        if($m->hasFish)         $mask += 4;
        if($m->hasLamb)         $mask += 8;
        if($m->hasPork)         $mask += 16;
        if($m->hasShellfish)    $mask += 32;

        return $mask;
    }

}

class MenuAssignerStrategyDummy extends MenuAssignerStrategy {
    public function GetUserMenus(User $user) {
        return [];
    }
}

class MenuAssignerStrategyNormal extends MenuAssignerStrategy {
    public function __construct($weekMenus) {
        $this->weekMenuList = $this->_getWeekMenuList($weekMenus);
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

        $weekMenuList->primaryMenu->replacements = [];
        $weekMenuList->vegMenu1->replacements = [];
        $weekMenuList->vegMenu2->replacements = [];
        $weekMenuList->omnMenu1->replacements = [];
        $weekMenuList->omnMenu2->replacements = [];

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
                            $weekMenuList->vegMenu2->replacements[] = $_replacementReason;
                            $menusToAssign[] = $weekMenuList->vegMenu2;
                        }
                    } else {
                        if($weekMenuList->vegMenu1) {
                            $weekMenuList->vegMenu1->replacements[] = $_replacementReason;
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
                            $weekMenuList->vegMenu2->replacements[] = $_replacementReason;
                            $menusToAssign[] = $weekMenuList->vegMenu2;
                        }
                    } else {
                        if($weekMenuList->vegMenu1) {
                            $weekMenuList->vegMenu1->replacements[] = $_replacementReason;
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


    private $weekMenuList;
}

class MenuAssignerStrategyExtended extends MenuAssignerStrategy {

    public function __construct($weekMenus) {
        $this->weekMenuList = $this->_getWeekMenuList($weekMenus);
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

        $assignedCount = 0;
        if($product->IsVegetarian()) {
            foreach($weekMenuList['veg'] as $_m) {
                $_m->replacements = [];
                $menusToAssign[] = $_m;
                ++$assignedCount;

                if(3 <= $assignedCount) break;
            }
        } else { // Omnivore

            $sMask = $this->_createSubscriptionMask($subscription->getAttributes()['dietary_preferences']);

            $replacements = [];

            foreach($weekMenuList['omni'] as $_m) {

                $_mMask = $this->_createMenuMask($_m);

                $_replacementReason = ($sMask & $_mMask) ^ $_mMask;
                $_replacementIsNecessary = (bool)$_replacementReason;

                if($_replacementIsNecessary) {
                    $replacements[] = $_replacementReason;
                    continue;
                }

                $_m->replacements = $replacements;
                $menusToAssign[] = $_m;
                ++$assignedCount;
                $replacements = [];
                if(3 <= $assignedCount) break;
            }
        }

        return $menusToAssign;

    }


    private function _getWeekMenuList($weekMenus) {
        $weekMenuList = [
            'veg' => [],
            'omni' => [],
        ];

//        shuffle($weekMenus); for testing sorting - comment for live


        $this->_fillWeekMenuList($weekMenuList, $weekMenus);

//foreach($weekMenuList['veg'] as $_m) {
//    echo ($_m->isVegetarian ? "V": " ").($_m->isOmnivore ? "O": " ")."\r\n";
//}
//echo "\r\n";

        $this->_sortWeekMenuVegList($weekMenuList['veg']);

//var_dump($weekMenuList['omni']);die();

//foreach($weekMenuList['veg'] as $_m) {
//    echo ($_m->isVegetarian ? "V": " ").($_m->isOmnivore ? "O": " ")."\r\n";
//}
//echo "----------------------------------\r\n";

//foreach($weekMenuList['omni'] as $_m) {
//    echo ($_m->isVegetarian ? "V": " ").($_m->isOmnivore ? "O": " ").($_m->vegetarianBackup ? "B" : ' ')."\r\n";
//}
//echo "\r\n";

        $this->_sortWeekMenuOmnivoreList($weekMenuList['omni']);

//foreach($weekMenuList['omni'] as $_m) {
//    echo ($_m->isVegetarian ? "V": " ").($_m->isOmnivore ? "O": " ").($_m->vegetarianBackup ? "B" : ' ')."\r\n";
//}
//echo "\r\n";

//die();

        return $weekMenuList;

    }

    protected function _fillWeekMenuList(&$weekMenuList, $weekMenus) {
        $veg = &$weekMenuList['veg'];
        $omni = &$weekMenuList['omni'];

        foreach($weekMenus as $_m) {
            if($_m->isVegetarian) {
                $veg[] = $_m;
                $omni[] = $_m;
                continue;
            }

            if ($_m->isOmnivore) {
                $omni[] = $_m;
                continue;
            }
        }
    }

    protected function _sortWeekMenuVegList(&$list) {

        usort($list, function($a, $b) {

            if($a->isOmnivore != $b->isOmnivore) {
                return -($a->isOmnivore - $b->isOmnivore);
            }
            return 0;
        });

    }

    protected function _sortWeekMenuOmnivoreList(&$list) {
        usort($list, function($a, $b) {

            if(!$a->isOmnivore && !$b->isOmnivore) {
                return -($a->vegetarianBackup - $b->vegetarianBackup);
            }

            if($a->isOmnivore != $b->isOmnivore) {
                return -($a->isOmnivore - $b->isOmnivore);
            }


            if($a->isOmnivore && $b->isOmnivore) {
                return ($a->isVegetarian - $b->isVegetarian);
            }

            return 0;
        });

    }

    private $weekMenuList;
}