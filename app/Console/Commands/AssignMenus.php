<?php

namespace App\Console\Commands;

use App\MenuAssigner;
use App\User;
use App\WhatsCookings;
use Illuminate\Console\Command;
use DB;


use App\UserSubscription;
use App\Product;

class AssignMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:menus {date?}';

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


    private function _fetchDeliveryDate() {

        $argumentDate = false;

        foreach($this->argument() as $key => $a) {
            if($key == 'command') continue;
            if(!$a) continue;

            list($_key, $_value) = explode('=', $a, 2);

            switch($_key) {
                case 'date':
                    $argumentDate = $_value;
                    break;

            }
        }


        if($argumentDate) {
            try {
                $theDate = new \DateTime($argumentDate);
            } catch (\Exception $e) {
                echo "Wrong Date";
                return;
            }
            $deliveryDate = $theDate->format('Y-m-d');
            $test = WhatsCookings::where('week_of', '=', $deliveryDate)->first();

            if(!$test) {
                echo("Wrong Date");
                return;
            }
        } else {
            $today = new \DateTime('now');

            $deliveryDate = WhatsCookings::where('week_of', '>', $today->format('Y-m-d'))
                ->min('week_of');
        }

        return $deliveryDate;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $deliveryDate = $this->_fetchDeliveryDate();

        $menuAssigner = new MenuAssigner(new \DateTime($deliveryDate));

        DB::table('menus_users')->where('delivery_date', '=', $deliveryDate)->delete();

        $insertArray = [];

        User::where('password', '<>', '')->chunk(20, function($users) use($menuAssigner, $deliveryDate, &$insertArray) {
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
                            'delivery_date' => $deliveryDate,
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

        DB::table("menus_users")->insert($insertArray);

        return true;
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