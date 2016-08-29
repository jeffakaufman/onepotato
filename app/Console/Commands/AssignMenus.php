<?php

namespace App\Console\Commands;

use App\Menu;
use App\Menus;
use App\MenusUsers;
use App\Product;
use App\User;
use App\UserSubscription;
use App\WhatsCookings;
use Illuminate\Console\Command;
use DB;

class AssignMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:menus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $today = new \DateTime('now');

        $nextDeliveryDate = WhatsCookings::where('week_of', '>', $today->format('Y-m-d'))
            ->min('week_of');

//var_dump($nextDeliveryDate);
        $this->_fillWeekPositions($this->_getCurrentMenus($nextDeliveryDate));

        DB::table('menus_users')->where('delivery_date', '=', $nextDeliveryDate)->delete();

        $insertArray = [];

        User::where('password', '<>', '')->chunk(20, function($users) use($nextDeliveryDate, &$insertArray) {
            foreach($users as $user) {
                /**
                 * @var App\User $user
                 */

                $subscription = UserSubscription::where('user_id', $user->id)->first();
                if(!$subscription) continue;
                /**
                 * @var UserSubscription $subscription
                 */


                $product = Product::find($subscription->product_id);
                if(!$product) continue;
                /**
                 * @var Product $product
                 */


                $menusToAssign = [];

                if($this->primaryMenu) {
                    $menusToAssign[] = $this->primaryMenu;
                }

                if($product->IsVegetarian()) {
                    if($this->vegMenu1) {
                        $menusToAssign[] = $this->vegMenu1;
                    }
                    if($this->vegMenu2) {
                        $menusToAssign[] = $this->vegMenu2;
                    }
                } else { // Omnivore
                    $replacementVegUsed = false;

                    $sMask = $this->_createSubscriptionMask($subscription->getAttributes()['dietary_preferences']);

                    //Process First Omnivore Meal
                    if($this->omnMenu1) {
                        $_replacementReason = ($sMask & $this->omnMenu1->mask) ^ $this->omnMenu1->mask;
                        $_replacementIsNecessary = (bool)$_replacementReason;

                        if($_replacementIsNecessary) {
//echo "Replacing {$_replacementReason}\r\n";
//printf("%06b \r\n%06b \r\n%06b \r\n%06b \r\n\r\n", $sMask, $this->omnMenu1->mask, $sMask & $this->omnMenu1->mask, ($sMask & $this->omnMenu1->mask) ^ $this->omnMenu1->mask);
                            if($replacementVegUsed) {
                                if($this->vegMenu2) {
                                    $menusToAssign[] = $this->vegMenu2;
                                }
                            } else {
                                if($this->vegMenu1) {
                                    $menusToAssign[] = $this->vegMenu1;
                                    $replacementVegUsed = true;
                                }
                            }
                        } else {
                            $menusToAssign[] = $this->omnMenu1;
                        }
                    }

                    //Process Second Omnivore Meal
                    if($this->omnMenu2) {
                        $_replacementReason = ($sMask & $this->omnMenu2->mask) ^ $this->omnMenu2->mask;
                        $_replacementIsNecessary = (bool)$_replacementReason;

                        if($_replacementIsNecessary) {
//echo "Replacing {$_replacementReason}\r\n";
//printf("%06b \r\n%06b \r\n%06b \r\n%06b \r\n\r\n", $sMask, $this->omnMenu2->mask, $sMask & $this->omnMenu2->mask, ($sMask & $this->omnMenu2->mask) ^ $this->omnMenu2->mask);

                            if($replacementVegUsed) {
                                if($this->vegMenu2) {
                                    $menusToAssign[] = $this->vegMenu2;
                                }
                            } else {
                                if($this->vegMenu1) {
                                    $menusToAssign[] = $this->vegMenu1;
                                    $replacementVegUsed = true;
                                }
                            }
                        } else {
                            $menusToAssign[] = $this->omnMenu2;
                        }
                    }
                }

                foreach($menusToAssign as $m) {
                    $insertArray[] = [
                        'menus_id' => $m->id,
                        'users_id' => $user->id,
                        'delivery_date' => $nextDeliveryDate,
                    ];
                }


//echo $product->IsOmnivore() ? 'O' : 'V';
//echo ' ';
//foreach($menusToAssign as $m) {
//    echo $m->isVegetarian ? "V({$m->id})" : "O({$m->id})";
//}

//$current = MenusUsers::where('users_id', $user->id)->where('delivery_date', '=', $nextDeliveryDate)->get();
//foreach($current as $ass) {
//    echo $ass->menus_id.' ';
//}

//echo "\r\n";


//var_dump($product->IsOmnivore());
//                $ac->UpdateRenewalDate($user, $renewalDate);
            }
        });

        DB::table("menus_users")->insert($insertArray);
    }


    private $primaryMenu;
    private $vegMenu1;
    private $vegMenu2;
    private $omnMenu1;
    private $omnMenu2;

    private function _getCurrentMenus($nextDeliveryDate) {

        $menus = DB::table('menus')
            ->join('menus_whats_cookings', 'menus.id', '=', 'menus_whats_cookings.menus_id')
            ->join('whats_cookings', 'whats_cookings.id', '=', 'menus_whats_cookings.whats_cookings_id')
            ->where('whats_cookings.week_of', '=', $nextDeliveryDate)
            ->get(['menus.*']);

        return $menus;
    }

    private function _fillWeekPositions($menus) {

//var_dump($menus);die();

        foreach($menus as $m) {
            $m->_marked = false;
        }

        // Find Primary Menu
        foreach($menus as $m) {
            if($m->isVegetarian && $m->isOmnivore) {
                $this->primaryMenu = $m;
                $m->_marked = true;
                break;
            }
        }

//echo count($menus);
        // Find Reserve Vegetarian Menu
        foreach($menus as $m) {
            if($m->_marked) continue;
            if($m->isVegetarian && $m->vegetarianBackup) {
                $this->vegMenu1 = $m;
                $m->_marked = true;
                break;
            }
        }
        if(!$this->vegMenu1) {
            foreach($menus as $m) {
                if($m->_marked) continue;
                if($m->isVegetarian) {
                    $this->vegMenu1 = $m;
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
                $this->vegMenu2 = $m;
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
                $this->omnMenu1 = $m;
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
                $this->omnMenu2 = $m;
                $m->_marked = true;
                break;
            }
        }
//echo count($menus);

//        var_dump($this->omnMenu1);
//        var_dump($this->omnMenu2);

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

}

/*
 * Red Meat -   1
 * Poultry -    2
 * Fish -       3
 * Lamb -       4
 * Pork -       5
 * Shellfish -  6
 * GF -         9
 * Nut Free     7
 *
	    	$subs = DB::table('products')
	    		->join('subscriptions','products.id','=','subscriptions.product_id')
	    		->get(['user_id as users_id',DB::raw($deliveryDate),DB::raw($menusID)]);


 class stdClass#738 (25) { MENU
    public $id =>
    int(38)
    public $created_at =>
    string(19) "2016-08-11 22:00:23"
    public $updated_at =>
    string(19) "2016-08-11 22:00:23"
    public $menu_description =>
    string(33) "with Spanish Rice and Black Beans"
    public $menu_title =>
    string(26) "Vegetarian Chicken Fajitas"
    public $menu_delivery_date =>
    NULL
    public $image =>
    string(98) "https://s3-us-west-1.amazonaws.com/onepotato-menu-cards/August2016/Vegetarian Chicken Fajitas.jpeg"
    public $isVegetarian =>
    int(1)
    public $isOmnivore =>
    int(0)
    public $hasBeef =>
    int(0)
    public $hasPoultry =>
    int(0)
    public $hasFish =>
    int(0)
    public $hasLamb =>
    int(0)
    public $hasPork =>
    int(0)
    public $hasShellfish =>
    int(0)
    public $hasNoGluten =>
    int(0)
    public $hasNuts =>
    int(0)
    public $vegetarianBackup =>
    int(0)
    public $noDairy =>
    int(0)
    public $noEgg =>
    int(0)
    public $noSoy =>
    int(0)
    public $oven =>
    int(0)
    public $stovetop =>
    int(0)
    public $slowcooker =>
    int(0)
    public $isNotAvailable =>
    int(0)

 */