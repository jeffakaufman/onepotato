<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 21/10/16
 * Time: 12:01
 */

namespace App;
use DB;


class ReportsBuilder {
    public function GetWeeklyKitchenReport(\DateTime $fromDate, \DateTime $toDate) {
        $query = DB::table('menus_users')
            ->join('users', 'users.id', '=', 'menus_users.users_id')
            ->leftJoin('shippingholds', function($join) {
                $join->on('shippingholds.user_id', '=', 'menus_users.users_id')
                    ->on('shippingholds.date_to_hold', '=', 'menus_users.delivery_date')
                    ->whereIn('shippingholds.hold_status', ['hold', 'held']);

            })
            ->whereDate('menus_users.delivery_date', '>=', $fromDate->format('Y-m-d'))
            ->whereDate('menus_users.delivery_date', '<=', $toDate->format('Y-m-d'))
            ->whereColumn('users.start_date', '<=', 'menus_users.delivery_date')
            ->whereIn('users.status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE, ])
            ->whereNull('shippingholds.id');

//        var_dump($query->getBindings());die();


        $dbData = $query->get();


        $byUserData = array();

        foreach($dbData as $dbRow) {
            $byUserData[$dbRow->users_id]['menuIds'][] = $dbRow->menus_id;
        }
//var_dump(count($byUserData));die();
        foreach($byUserData as &$_x) {
            sort($_x['menuIds']);
            $_x["hash"] = implode(',', $_x['menuIds']);

            foreach($_x['menuIds'] as $mId) {
                $_x['menus'][$mId] = $this->_getMenu($mId);
            }

            $_x['bigGroup'] = $this->_getBigGroup($_x);
//            $_x['groupTitle'] = $this->_getGroupTitle($_x);
        }


        $reportData = [];
        $reportData['bigGroups'] = [];
        $reportData['total'] = ['count' => 0];
        foreach($byUserData as $userId => $buRow) {
            if(!isset($reportData['bigGroups'][$buRow['bigGroup']])) {
                $bigGroup = [
                    'code' => $buRow['bigGroup'],
                    'name' => $this->_getBigGroupName($buRow['bigGroup']),
                    'groups' => [],
                ];
                $reportData['bigGroups'][$buRow['bigGroup']] = $bigGroup;
            }

            $bgPtr = &$reportData['bigGroups'][$buRow['bigGroup']];

            if(!isset($bgPtr['groups'][$buRow['hash']])) {
                $group = [
                    'code' => $buRow['hash'],
                    'name' => $this->_getGroupTitle($buRow),
                    'products' => [],
                ];

                $bgPtr['groups'][$buRow['hash']] = $group;
            }

            $gPtr = &$bgPtr['groups'][$buRow['hash']];

            $subscription = UserSubscription::GetByUserId($userId);
            $product = Product::find($subscription->product_id);

            $sku = ProductSku::BuildByText($product->sku);

            $productHash = $sku->GetNumAdults()."-".$sku->GetNumChildren()."-".(int)$sku->IsGlutenFree();

            if(!isset($gPtr['products'][$productHash])) {
                $gPtr['products'][$productHash] = [
                    'sku' => $product->sku,
                    'name' => $this->_getProductCaption($sku),
                    'count' => 0,
                ];
            }

            $pPtr = &$gPtr['products'][$productHash];
            ++$pPtr['count'];
            ++$reportData['total']['count'];
        }


        foreach($reportData['bigGroups'] as &$_bgData) {
            foreach($_bgData['groups'] as &$_gData) {
                ksort($_gData['products']);
                foreach($_gData['products'] as &$_pData) {
                }
            }
        }

        return $reportData;
    }


    private function _getProductCaption(ProductSku $sku) {
        $caption = "{$sku->GetNumAdults()} Adults";
        if($sku->GetNumChildren() > 0) {
            $caption .= " and {$sku->GetNumChildren()} Child".($sku->GetNumChildren() > 1 ? 'ren' : '');
        }

        if($sku->IsGlutenFree()) {
            $caption .= " - Gluten Free";
        }

        return $caption;
    }

    private function _getBigGroupName($code) {
        $name = "UNKNOWN";

        switch($code) {
            case 'otherOmnivore':
                $name = 'Other Omnivore Boxes';
                break;
            case 'standardOmnivore':
                $name = 'Standard Omnivore';
                break;
            case 'standardVegetarian':
                $name = 'Standard Vegetarian';
                break;
        }

        return $name;
    }

    private function _getGroupTitle($data) {
        if(!isset($this->_groupTitleCache[$data['hash']])) {
            $menuNames = [];
            foreach($data['menus'] as $m) {
                $menuNames[] = $m->menu_title;
            }

            $this->_groupTitleCache[$data['hash']] = implode(",", $menuNames);
        }

        return $this->_groupTitleCache[$data['hash']];
    }

    private $_groupTitleCache = [];

    private function _getMenu($id) {
        if(!isset($this->_menuCache[$id])) {
            $this->_menuCache[$id] = Menu::find($id);
        }
        return $this->_menuCache[$id];
    }

    private function _getBigGroup($data) {
        if(!isset($this->_bigGroupCache[$data['hash']])) {
            $code = '';
            foreach($data['menus'] as $m) {
                if($m->isVegetarian && $m->isOmnivore) {
                    $code .= 'B';
                } elseif ($m->isOmnivore) {
                    $code .= 'O';
                } else {
                    $code .= 'V';
                }
            }

            $bigGroup = 'otherOmnivore';
            switch($code) {
                case 'OOO':
                case 'OOB':
                case 'OBO':
                case 'BOO':
                case 'OBB':
                case 'BOB':
                case 'BBO':
                case 'BBB':
                    $bigGroup = 'standardOmnivore';
                    break;

                case 'VVV':
                case 'VVB':
                case 'VBV':
                case 'BVV':
                case 'VBB':
                case 'BVB':
                case 'BBV':
                    $bigGroup = 'standardVegetarian';
                    break;
            }

//var_dump($code);
//var_dump('==================================');
            $this->_bigGroupCache[$data['hash']] = $bigGroup;
        }
        return $this->_bigGroupCache[$data['hash']];
    }

    private $_bigGroupCache = [];

    private $_menuCache = [];



    public function showReports()
    {
        // last week is the last week we have "shipped" invoices for
        $lastPeriodEndDate = Subinvoice::max('ship_date');
        $lastPeriodEndDate = date('Y-m-d',strtotime($lastPeriodEndDate."+8 days"));



        $lastTuesday = date('Y-m-d',strtotime($lastPeriodEndDate.'-7 day'));
        $thisTuesday = date('Y-m-d',strtotime($lastTuesday . '+7 days'));
        $nextTuesday = date('Y-m-d',strtotime($thisTuesday . '+7 days'));

        $shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
            ->where('date_to_hold', "=",$thisTuesday)
            ->get();
        $skipIdsThisWeek = array_pluck($shippingHoldsWeek, 'user_id');

        //This week
        //yeah, i could've done this with a DB query but this seemed easier to read.
        $skipsThisWeek = User::whereIn('users.status',['active','inactive'])
            ->where('start_date', "<=",$thisTuesday)
            ->whereIn('id', $skipIdsThisWeek)
            ->get();

        $activeThisWeek = User::whereIn('users.status',['active','inactive'])
            ->where('start_date', "<=",$thisTuesday)
            ->orderBy('name','asc')
            ->whereNotIn('id', $skipIdsThisWeek)
            ->get();

        //last week
        $shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
            ->where('date_to_hold', "=",$lastTuesday)
            ->get();
        $skipIdsLastWeek = array_pluck($shippingHoldsWeek, 'user_id');

        $skipsLastWeek = User::whereIn('users.status',['active','inactive'])
            ->where('start_date', "<=",$lastTuesday)
            ->whereIn('id', $skipIdsLastWeek)
            ->get();

        $shippedLastWeek = Subinvoice::where('invoice_status','shipped')
            ->where('period_end_date', ">=",$lastPeriodEndDate)
            ->get();
        $skipIdsLastWeek = array_pluck($shippedLastWeek, 'user_id');

        $shippedLastWeek = User::whereIn('id', $skipIdsLastWeek)
            ->orderBy('name','asc')
            ->get();

        //next week
        $shippingHoldsWeek = Shippingholds::whereIn('hold_status',['hold','held'])
            ->where('date_to_hold', "=",$nextTuesday)
            ->get();
        $skipIdsNextWeek = array_pluck($shippingHoldsWeek, 'user_id');

        $skipsNextWeek = User::whereIn('users.status',['active','inactive'])
            ->where('start_date', "<=",$nextTuesday)
            ->whereIn('id', $skipIdsNextWeek)
            ->get();

        $activeNextWeek = User::whereIn('users.status',['active','inactive'])
            ->where('start_date', "<=",$nextTuesday)
            ->whereNotIn('id', $skipIdsNextWeek)
            ->get();

        $nextTotalSubs = DB::table('users')
            ->select('users.status', DB::raw('count(*) as total'))
            ->leftJoin('shippingholds as shippingholds', 'shippingholds.user_id', '=', 'users.id')
            ->groupBy('users.status')
            ->orderBy('users.status')
            ->get();

        $skips = DB::table('users')
            ->select('date_to_hold', DB::raw('count(*) as total'))
            ->join('shippingholds','shippingholds.user_id','=','users.id')
            ->where('date_to_hold', ">=",date('Y-m-d H:i:s'))
            ->where('hold_status', "=",'hold')
            ->groupBy('date_to_hold')
            ->orderBy('date_to_hold')
            ->get();

        /***************************************************************************************
         *
         *	Standard Omnivore Meals
         *
         *****************************************************************************************/

        $omnivoreMeals = App\WhatsCookings::where('week_of',$thisTuesday)
            ->first()
            ->getOmnivoreMeals()
            ->orderBy('menu_title')
            ->get();

        $omnivoreMealsID = array_pluck($omnivoreMeals, 'id');

        $omnivoreUsersID =  DB::table('users')
            ->select('users.id')
            ->join('menus_users','menus_users.users_id','=','users.id')
            ->where('start_date','<=',$thisTuesday)
            ->whereNotIn('users.id',$skipIdsThisWeek)
            ->whereIn('users.status',['active','inactive'])
            ->whereIn('users_id',function($q) use ($omnivoreMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$omnivoreMealsID[0]);
            })
            ->whereIn('users_id',function($q) use ($omnivoreMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$omnivoreMealsID[1]);
            })
            ->whereIn('users_id',function($q) use ($omnivoreMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$omnivoreMealsID[2]);
            })
            ->groupBy('users.id')
            ->pluck('id');

        $thisWeeksStandardOmnivore = DB::table('products')
            ->select('products.product_title',DB::raw('count(*) as total'))
            ->join('subscriptions','subscriptions.product_id','=','products.id')
            ->whereIn('subscriptions.user_id', $omnivoreUsersID)
            ->groupBy('product_title')
            ->orderBy('product_title')
            ->get();

        $standardOmnivoreBoxes = new stdClass();
        $standardOmnivoreBoxes -> counts = $thisWeeksStandardOmnivore;
        $standardOmnivoreBoxes -> names = array_pluck($omnivoreMeals,'menu_title');

        /***************************************************************************************
         *
         *	Standard Vegetarian Meals
         *
         *****************************************************************************************/

        $vegetarianMeals = App\WhatsCookings::where('week_of',$thisTuesday)
            ->first()
            ->getVegetarianMeals()
            ->orderBy('menu_title')
            ->get();
        $vegetarianMealsID = array_pluck($vegetarianMeals, 'id');

        $vegetarianUsersID =  DB::table('users')
            ->select('users.id')
            ->join('menus_users','menus_users.users_id','=','users.id')
            ->where('start_date','<=',$thisTuesday)
            ->whereNotIn('users.id',$skipIdsThisWeek)
            ->whereIn('users.status',['active','inactive'])
            ->whereIn('users_id',function($q) use ($vegetarianMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$vegetarianMealsID[0]);
            })
            ->whereIn('users_id',function($q) use ($vegetarianMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$vegetarianMealsID[1]);
            })
            ->whereIn('users_id',function($q) use ($vegetarianMealsID) {
                $q->from('menus_users')
                    ->selectRaw('users_id')
                    ->where('menus_id',$vegetarianMealsID[2]);
            })
            ->groupBy('users.id')
            ->pluck('id');

        $thisWeeksStandardVegetarian = DB::table('products')
            ->select('products.product_title',DB::raw('count(*) as total'))
            ->join('subscriptions','subscriptions.product_id','=','products.id')
            ->whereIn('subscriptions.user_id', $vegetarianUsersID)
            ->groupBy('product_title')
            ->orderBy('product_title')
            ->get();

        $thisWeeksStandardVegetarianMenus = Menus::whereIn('id',$vegetarianMealsID)->get();
        $vegetarianBoxes = new stdClass();
        $vegetarianBoxes -> counts = $thisWeeksStandardVegetarian;
        $vegetarianBoxes -> names = Menus::whereIn('id',$vegetarianMealsID)->get()->pluck('menu_title');

        /***************************************************************************************
         *
         *	All other combinations
         *
         *****************************************************************************************/

        //now get everybody who didn't get a standard box...
        $everybodyElseRaw = App\MenusUsers::where('delivery_date',$thisTuesday)
            ->whereNotIn('users_id', $skipIdsThisWeek)
            ->whereNotIn('users_id', array_merge($omnivoreUsersID,$vegetarianUsersID))
            ->whereHas('users',function($q) use ($thisTuesday) {
                $q->whereIn('status',['active','inactive'])
                    ->where('start_date','<=',$thisTuesday);
            })
            ->orderBy('users_id')
            ->orderBy('menus_id')
            ->get();
        //...create an array if everybody else in the system and their combination of menus for the week
        $menuPivotRow = new stdClass();
        $everybodyElse = [];
        foreach ($everybodyElseRaw as $i => $menu) {
            $menuPivotRow->user_id = $menu->users_id;
            $menuPivotRow->menu[$i%3] = $menu->menus_id;
            if ($i%3 == 2) {
                array_push($everybodyElse,$menuPivotRow);
                $menuPivotRow = new stdClass();
            }
        }

        $everybodyElseIds = array_pluck($everybodyElse,'user_id');
        $everybodyElseMenus = array_pluck($everybodyElse,'menu');
        sort($everybodyElseMenus);

        //...and remove duplicate values from the array to get the other menu combinations
        $everybodyElseMenus = array_values (array_unique($everybodyElseMenus, SORT_REGULAR));

        $otherBoxCounts = [];
        $otherBoxes = [];
        foreach ($everybodyElseMenus as $i=>$everybodyElseMenu)	 {

            $customBoxUserIds =  DB::table('users')
                ->select('users.id')
                ->join('menus_users','menus_users.users_id','=','users.id')
                ->whereIn('users.id',$everybodyElseIds)
                ->whereIn('users_id',function($q) use ($everybodyElseMenu) {
                    $q->from('menus_users')
                        ->selectRaw('users_id')
                        ->where('menus_id',$everybodyElseMenu[0]);
                })
                ->whereIn('users_id',function($q) use ($everybodyElseMenu) {
                    $q->from('menus_users')
                        ->selectRaw('users_id')
                        ->where('menus_id',$everybodyElseMenu[1]);
                })
                ->whereIn('users_id',function($q) use ($everybodyElseMenu) {
                    $q->from('menus_users')
                        ->selectRaw('users_id')
                        ->where('menus_id',$everybodyElseMenu[2]);
                })
                ->groupBy('users.id')
                ->pluck('id');
            $otherBoxCounts[$i] = DB::table('products')
                ->select('products.product_title',DB::raw('count(*) as total'))
                ->join('subscriptions','subscriptions.product_id','=','products.id')
                ->whereIn('subscriptions.user_id', $customBoxUserIds)
                ->groupBy('product_title')
                ->orderBy('product_title')
                ->get();

            $boxScore = new stdClass();
            $boxScore -> counts = $otherBoxCounts[$i];
            $boxScore -> names = Menus::whereIn('id',$everybodyElseMenus[$i])->orderBy('menu_title')->get()->pluck('menu_title');
            $otherBoxes[$i] = $boxScore;
        }

        $newSubs = DB::table('users')
            ->select('start_date','products.product_description', DB::raw('count(*) as total'))
            ->join('subscriptions','subscriptions.user_id','=','users.id')
            ->join('products','subscriptions.product_id','=','products.id')
            ->groupBy('start_date','products.product_description')
            ->orderBy('start_date','products.product_description')
            ->where('start_date', ">",date('Y-m-d H:i:s'))
            ->where('subscriptions.stripe_id', "<>","0")
            ->get();

        $totalNewSubs = DB::table('users')
            ->select('subscriptions.status', DB::raw('count(*) as total'))
            ->join('subscriptions','subscriptions.user_id','=','users.id')
            ->join('products','subscriptions.product_id','=','products.id')
            ->whereNotNull('subscriptions.status')
            ->where('start_date', ">",date('Y-m-d H:i:s'))
            ->where('subscriptions.stripe_id', "<>","0")
            ->groupBy('subscriptions.status')
            ->orderBy('subscriptions.status')
            ->get();

        return view('admin.reports')
            ->with(['oldDate'=>''
                    ,'oldMenu'=>''
                    ,'newSubs'=>$newSubs
                    ,'activeThisWeek'=>$activeThisWeek
                    ,'skipsThisWeek'=>$skipsThisWeek
                    ,'activeNextWeek'=>$activeNextWeek
                    ,'skipsNextWeek'=>$skipsNextWeek
                    ,'skipsLastWeek'=>$skipsLastWeek
                    ,'shippedLastWeek'=>$shippedLastWeek
                    ,'thisTuesday'=>date('F d', strtotime($thisTuesday))
                    ,'otherBoxes'=>$otherBoxes
                    ,'vegetarianBoxes'=>$vegetarianBoxes
                    ,'standardOmnivoreBoxes'=>$standardOmnivoreBoxes
                    ,'skips'=>$skips]
            );




    }

}