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

    public static function AssignManually(User $user, \DateTime $deliveryDate, array $menuIds, $comments = false) {
        $set = new UserMenuSet($user, $deliveryDate);

        $menuIds = array_values($menuIds);
        if(count($menuIds) >= 3) {
            $set->SetMenuIds($menuIds[0], $menuIds[1], $menuIds[2]);
            $set->Save($comments);
        }
    }

    public static function RestoreDefault(User $user, \DateTime $deliveryDate) {
        $set = new UserMenuSet($user, $deliveryDate);
        $set->RestoreDefault();
    }

    /**
     * @param \DateTime $deliveryDate
     * @return MenuAssigner
     */
    public static function GetInstance(\DateTime $deliveryDate) {
        $hash = $deliveryDate->format('Y-m-d');
        if((!isset(self::$_repository[$hash])) || (!(self::$_repository[$hash] instanceof self))) {
            self::$_repository[$hash] = new self($deliveryDate);
        }
        return self::$_repository[$hash];
    }

    private static $_repository = [];

    public static function ReassignAllForDate(\DateTime $deliveryDate, $force = false, $comments = false) {

        User::where('password', '<>', '')->chunk(20, function($users) use($deliveryDate, $force, $comments) {
            foreach($users as $user) {
                /**
                 * @var User $user
                 */

                try {
                    $set = new UserMenuSet($user, $deliveryDate);
//var_dump($set->IsOverwritten());
//var_dump($set->MatchDefault());
//die();
                    if($force || !$set->IsOverwritten()) {
                        $set->RestoreDefault($comments);
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


class UserMenuSet {

    public function GetFirstId() {
        return $this->menuIds[0];
    }

    public function GetSecondId() {
        return $this->menuIds[1];
    }

    public function GetThirdId() {
        return $this->menuIds[2];
    }

    public function SetMenuIds($menuId_1, $menuId_2, $menuId_3) {

        if(
            in_array($menuId_1, $this->menuIds)
            && in_array($menuId_2, $this->menuIds)
            && in_array($menuId_3, $this->menuIds)
        ) { // The Same, no need to make any changes
            return;
        }

        $this->menuIds = [
            $menuId_1,
            $menuId_2,
            $menuId_3,
        ];

        $this->_cleanUp();

        $this->_needToSave = true;
    }

    private $_needToSave = false;

    public function Save($comments = false) {

        if(!$this->_needToSave) return;

        $changed = !$this->MatchDefault();

        foreach($this->assignments as $_key => $a) {
            /**
             * @var UserMenuAssignment $a
             */

            if(isset($this->menusUsersList[$_key])) { // Save to current record
                $record = $this->menusUsersList[$_key];
            } else { // Create new record
                $record = new MenusUsers();
                $record->users_id = $this->user->id;
                $record->delivery_date = $this->deliveryDate->format('Y-m-d');
            }

            $record->menus_id = $a->GetMenuId();

            if($changed) {
                $record->instead_of = $a->GetDefaultId();
            } else {
                $record->instead_of = null;
            }

            if($comments) {
                $record->change_comments = $comments;
            }

            $record->save();
        }

        $logger = new SimpleLogger("MenuChanges_for_{$this->deliveryDate->format('Y-m-d')}.log");
        $logger->Log("#{$this->user->id} [{$this->user->email}] {$this->user->first_name} {$this->user->last_name} for {$this->deliveryDate->format('Y-m-d')} :: ".($comments ? $comments : "No comments"));


        $this->_needToSave = false;

        $this->_grabCurrent();
    }

    public function RestoreDefault($comments = false) {
        $this->SetMenuIds($this->defaultMenuIds[0], $this->defaultMenuIds[1], $this->defaultMenuIds[2]);
        $this->Save($comments);
    }

    public function IsOverwritten() {
        $response = false;

        foreach($this->menusUsersList as $_m) {
            if($_m->instead_of) {
                $response = true;
                break;
            }
        }

        return $response;
    }

    public function MatchDefault() {
        $response = true;
        foreach($this->assignments as $assignment) {
            /**
             * @var UserMenuAssignment $assignment
             */

            if(!$assignment->IsMatch()) {
                $response = false;
                break;
            }
        }

        return $response;
    }

    public function __construct(User $user, \DateTime $deliveryDate) {
        $this->user = $user;
        $this->deliveryDate = $deliveryDate;

        $this->assigner = MenuAssigner::GetInstance($deliveryDate);

        $this->_grabCurrent();
    }

    private function _has($menuId) {
        return in_array($menuId, $this->menuIds);
    }

    private function _grabCurrent() {
        $this->menusUsersList = MenusUsers::where('users_id', '=', $this->user->id)
            ->where('delivery_date', '=', $this->deliveryDate->format('Y-m-d'))
            ->get();

        if(count($this->menusUsersList) < 3) {
            $this->_needToSave = true;
        }

        $this->menuIds = [];
        foreach($this->menusUsersList as $_m) {
            $this->menuIds[] = $_m->menus_id;
        }

        $this->defaultMenuIds = [];
        foreach($this->assigner->GetUserMenus($this->user) as $_menu) {
            $this->defaultMenuIds[] = $_menu->id;
        }

        $this->_cleanUp();
    }

    private function _cleanUp() {
        $this->assignments = [];

        // Fill with default IDs if count is < 3
        if(3 > count($this->menuIds)) {
            for($i = count($this->menuIds); $i < 3; $i++) {
                foreach($this->defaultMenuIds as $dmId) {
                    if(!in_array($dmId, $this->menuIds)) {
                        $this->menuIds[] = $dmId;
                        break;
                    }
                }
            }
        }

        $pairs = [];

        foreach($this->defaultMenuIds as $dmId) {
            if(in_array($dmId, $this->menuIds)) {
                $pairs[$dmId] = $dmId;
            } else {
                foreach($this->menuIds as $mId) {
                    if(in_array($mId, $this->defaultMenuIds)) continue;
                    if(in_array($mId, $pairs)) continue;
                    $pairs[$dmId] = $mId;
                    break;
                }
            }
        }
//var_dump($pairs);
        foreach($pairs as $dmId => $mId) {
            $assignment = new UserMenuAssignment();
            $assignment->SetMenuId($mId);
            $assignment->SetDefaultId($dmId);
            $this->assignments[] = $assignment;
        }
    }


    private $defaultMenuIds = [];

    private $menusUsersList = [];

    private $menuIds = [];

    private $assignments = [];

    /**
     * @var \DateTime
     */
    private $deliveryDate;

    /**
     * @var User
     */
    private $user;

    /**
     * @var MenuAssigner
     */
    private $assigner;
}

class UserMenuAssignment {

    public function GetMenuId() {
        return $this->menuId;
    }

    public function GetDefaultId() {
        return $this->defaultId;
    }

    public function SetMenuId($id) {
        $this->menuId = $id;
    }

    public function SetDefaultId($id) {
        $this->defaultId = $id;
    }

    public function IsMatch() {
        return ($this->menuId == $this->defaultId);
    }

    private $menuId;
    private $defaultId;
}