<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Menus;
use App\MenusUsers;
use App\Credit;
use App\User;
use App\Shippingholds;
use App\Subinvoice;
use App\Shipping_address;
use App\UserSubscription;
use App\Product;
use CountryState;
use App;
use stdClass;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
//     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
	    	$users = $this->_getUsersList();
			return view('admin.users.users')->with(['users'=>$users, 'params' => $this->_getListParams()]);
    }


    private function _getUsersList() {

        $params = $this->_getListParams();
//var_dump($params);die();
        $query = DB::table('users')
            ->select('users.id','users.email','users.name', 'users.start_date', 'users.status', DB::raw('sum(subinvoices.charge_amount/100) as revenue'))
            ->join('subscriptions','users.id','=','subscriptions.user_id')
            ->join('subinvoices','users.id','=','subinvoices.user_id')
            ->where('subscriptions.stripe_id', '<>', '')
            ->where('subscriptions.name', '<>', '');

            if(isset($params['filterText'])) {
                $query->where(function($query) use ($params){
                    $query->where('users.name', 'like', '%'.$params['filterText'].'%')
                        ->orWhere('users.email', 'like', '%'.$params['filterText'].'%');
                });
            }

            $query->orderBy($params['orderBy'], $params['orderDir']);

            $query->orderBy('users.name', 'asc')
            ->groupBy('users.id');

         return $query->get();
    }

    private function _getListParams() {
        $params = [
            'orderBy' => 'name',
            'orderDir' => 'asc',

            'filterText' => '',
        ];

        $sessionParams = session('usersListParams');
        if($sessionParams) {
            $params = array_merge($params, $sessionParams);
        }

        return $params;
    }

    public function updateListParams(Request $request, $type, $value = '') {
        $currentParams = $this->_getListParams();
        $sessionData = session('usersListParams');
//var_dump($sessionData);die();
        if(!$sessionData) {
            $sessionData = [];
        }

        switch($type) {
            case 'orderBy':
                $mapping = [
                    'userName' => [
                        'field' => 'users.name',
                        'dir' => 'asc',
                    ],
                    'email' => [
                        'field' => 'users.email',
                        'dir' => 'asc',
                    ],
                    'startDate' => [
                        'field' => 'users.start_date',
                        'dir' => 'desc',
                    ],
                    'revenue' => [
                        'field' => 'revenue',
                        'dir' => 'desc',
                    ],
                    'status' => [
                        'field' => 'subscriptions.status',
                        'dir' => 'asc',
                    ],
                ];

                if(isset($mapping[$value])) {
                    $_value = $mapping[$value]['field'];
                    if($currentParams['orderBy'] == $_value) {
                        $sessionData['orderDir'] = $currentParams['orderDir'] == 'asc' ? 'desc' : 'asc';
                    } else {
                        $sessionData['orderBy'] = $_value;
                        $sessionData['orderDir'] = $mapping[$value]['dir'];
                    }


                } else {
                    //Do Nothing
                }

                break;

            case 'filterText':
                $sessionData['filterText'] = $value;
                break;

            default:
                //Do Nothing
                break;
        }

//var_dump($type);
//var_dump($value);

        session(['usersListParams' => $sessionData]);
        return redirect("/admin/users");
    }

    public function showUserDetails($id)
    {
    	$user = User::find($id);
		$states = CountryState::getStates('US');
		$shippingAddress = App\Shipping_address::where('user_id',$id)
							->where('is_current', 1)
							->orderBy('id', 'desc')
							->first();
		
		$csr_notes = App\Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();

		$userSubscription = App\UserSubscription::where('user_id',$id)->first();

		if ($userSubscription) {
			$productID = $userSubscription->product_id;
			$userProduct = App\Product::where('id',$productID)->firstOrFail();
		}

		$referrals = App\Referral::where('referrer_user_id',$id)->get();
		
		$upcomingMenus = $user->menus()->where('delivery_date','>',date('Y-m-d'))->orderBy('delivery_date')->get();
		$upcomingDeliveries = new stdClass;
		$weeksMenus = [] ;
		$oldDate = "";

		
		foreach ($upcomingMenus as $i => $upcomingMenu) {
			if ($upcomingMenu->pivot->delivery_date <> $oldDate) {
				$upcomingDeliveries->delivery_date = $upcomingMenu->pivot->delivery_date;
				$upcomingDeliveries->weekMenu[0] = $upcomingMenu->menu_title;
				$upcomingDeliveries->skipStatus = $user->getSkips()->where('date_to_hold',$upcomingDeliveries->delivery_date)->first();
				$oldDate = $upcomingMenu->pivot->delivery_date;
			} else {
				$upcomingDeliveries->weekMenu[$i%3] = $upcomingMenu->menu_title;
			}
			if ($i%3 == 2) {
				array_push($weeksMenus,$upcomingDeliveries);
				$upcomingDeliveries = new stdClass();
			}
		}
		
		$upcomingSkipsNoMenu = $user->getSkips()
				->where('date_to_hold','>',date('Y-m-d'))
				->whereNotIn('date_to_hold',array_pluck($weeksMenus,'delivery_date'))
				->orderBy('date_to_hold')
				->get();

/*		
		
		$weeksMenus = App\MenusUsers::where('users_id',$id)->get();
		$weeksMenus = DB::table('menus_users')
					->select( DB::raw('DISTINCT delivery_date, hold_status,menu_title') )
					->where('menus_users.users_id',$id)
					->where('delivery_date','>',date('Y-m-d'))
					->join('menus','menus.id','=','menus_users.menus_id')
					->leftJoin('shippingholds', function($join)
                        {
                             $join->on('shippingholds.user_id','=','menus_users.users_id');
                             $join->on('shippingholds.date_to_hold','=','menus_users.delivery_date');
                    	})
						->Orderby('delivery_date')
					->get();
		$weeksMenusDates = array_pluck($weeksMenus,'delivery_date');


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
		$upcomingSkipsNoMenu = App\Shippingholds::where('user_id',$id)
						->where('date_to_hold','>=',date('Y-m-d'))
						->whereNotIn('date_to_hold',$weeksMenusDates)
						->Orderby('date_to_hold')
						->get();
*/



						
		$credits = App\Credit::where('user_id',$id)->get();

		return view('admin.users.user_details')
				->with(['user'=>$user,
						'shippingAddress'=>$shippingAddress,
						'userSubscription'=>$userSubscription,
						'csr_notes'=>$csr_notes,
						'userProduct'=>$userProduct,
						'states'=>$states,
						'referrals'=>$referrals,
						'weeksMenus'=>$weeksMenus,
						'upcomingSkipsNoMenu'=>$upcomingSkipsNoMenu,
						'credits'=>$credits,
						]);

    }


	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showRecipes()
    {

		$recipes = Recipes::get();
		return view('recipes')->with(['recipes'=>$recipes]);;

    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showRecipe($id)
    {
		$recipe = Recipes::find($id);
		return view('recipe')->with(['recipe'=>$recipe]);;

    }

	public function saveRecipe(Request $request) {
		/*
		
		$validator = Validator::make($request->all(), [
	        'recipe_title' => 'required|max:255',
		    'recipe_description' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/recipes')
	            ->withInput()
	            ->withErrors($validator);
	    }
*/
	    $recipe = new recipes;
	    $recipe->recipe_title = $request->recipe_title;
		$recipe->recipe_type = $request->recipe_type;
		$recipe->photo_url = $request->photo_url;
		$recipe->pdf_url = $request->pdf_url;
		$recipe->video_url = $request->video_url;
	    $recipe->save();

	    return redirect('/recipes');
	
	}
	
    public function showReports()
    {
        // last week is the last week we have "shipped" invoices for
        $lastPeriodEndDate = Subinvoice::where('invoice_status','shipped')->max('period_end_date');
        $lastPeriodEndDate = date('Y-m-d',strtotime($lastPeriodEndDate));           
        $lastPeriodEndDate = '2016-10-11';
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
	    		->where('start_date','<=',"2016-10-11")
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
	    		->where('start_date','<=',"2016-10-11")
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
    


    public function EditShippingAddress($userId, $shId) {
        $shippingAddress = Shipping_address::find($shId);
        return view("admin.users.parts.shipping_address")->with(["shippingAddress" => $shippingAddress]);
    }

    public function SaveShippingAddress($userId, $shId) {
        $request = request();
        $sh = Shipping_address::find($shId);
        $sh->shipping_address = $request->address1;
        $sh->shipping_address_2 = $request->address2;
        $sh->shipping_city = $request->city;
        $sh->shipping_state = $request->state;
        $sh->shipping_zip = $request->zip;
        $sh->save();

        $user = User::find($userId);
        return view("admin.users.parts.shipping_address_view")->with(["shippingAddress" => $sh, 'user' => $user]);
    }

    public function EditUserProduct($userId, Request $request) {
        $user = User::find($userId);

        $userSubscription = UserSubscription::where('user_id',$userId)->first();
        $userSubscription->status = "active";
        $plan_id = $userSubscription->product_id;

        $product = Product::where('id', $plan_id)->first();

        return view("admin.users.parts.edit_product")->with([
            "user" => $user,
            "userProduct" => $product,
            "userSubscription" => $userSubscription,
            'changeDate' => $this->_getChangeDate(),
        ]);
    }

    public function SaveUserProduct($userId) {
        $request = request();

        $userSubscription = UserSubscription::where('user_id',$userId)->first();

        $plan_type = $request->plan_type;
        $plan_size = $request->plan_size;
        $num_kids = $request->children;
        $gluten_free = $request->gluten_free;
        $theSKU = '';


        if ($plan_type=='Vegetarian Box') {
            $theSKU = "01";
        }
        if ($plan_type=='Omnivore Box') {
            $theSKU = "02";
        }

        //num adults defaults to 02
        $theSKU .= "02";

        if ($plan_size=="family") {

            if ($num_kids=="0") {$theSKU .= "00";}
            if ($num_kids=="1") {$theSKU .= "01";}
            if ($num_kids=="2") {$theSKU .= "02";}
            if ($num_kids=="3") {$theSKU .= "03";}
            if ($num_kids=="4") {$theSKU .= "04";}

        }else{
            $theSKU .= "00";
        }

        if ($request->prefs && in_array('9', $request->prefs)) {
            $theSKU .= "0100";
        }else{
            $theSKU .= "0000";
        }


        //look up the product ID
        $newProduct = Product::where('sku',$theSKU)->first();

        $userSubscription->product_id = $newProduct->id;
        if (isset($request->prefs)) {
            $userSubscription->dietary_preferences = implode(",",$request->prefs);
        }

        $userSubscription->save();

        //make sure trial_ends is set the same -
/*
        //update STRIPE
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id);

        //$period_start = $subscription->current_period_start;
        //$period_end = $subscription->current_period_end;
        $trial_end = $subscription->trial_end;

        $subscription->plan = $newProduct->stripe_plan_id;
        $subscription->prorate = false;
        //$subscription->current_period_end = $period_end;
        //$subscription->current_period_start = $period_start;
        if (isset($trial_end)) {
            $subscription->trial_end = $trial_end;
        }

        $subscription->save();
*/
        return redirect("/admin/user_details/{$userId}");

    }

    private function _getChangeDate() {
        $changeDate = '';
        $today = date('N');
        if		($today == 1)	{ $changeDate = date('l, F jS', strtotime("+8 days"));  }
        elseif	($today == 2)	{ $changeDate = date('l, F jS', strtotime("+7 days"));  }
        elseif	($today == 3)	{ $changeDate = date('l, F jS', strtotime("+6 days"));  }
        elseif	($today == 4)	{ $changeDate = date('l, F jS', strtotime("+12 days")); }
        elseif	($today == 5)	{ $changeDate = date('l, F jS', strtotime("+11 days")); }
        elseif	($today == 6)	{ $changeDate = date('l, F jS', strtotime("+10 days")); }
        elseif	($today == 7)	{ $changeDate = date('l, F jS', strtotime("+9 days"));  }
        return $changeDate;
    }


    public function RestartSubscription($userId) {
//        $request = request();

        $user = User::find($userId);
        $user->status = User::STATUS_ACTIVE;
        $customer_stripe_id = $user->stripe_id;

        //retrieve stripe ID from subscriptions table
        $userSubscription = UserSubscription::where('user_id',$userId)->first();
        $userSubscription->status = "active";
        $plan_id = $userSubscription->product_id;

        $product = Product::where('id', $plan_id)->first();
        $stripe_plan_id = $product->stripe_plan_id;

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $trial_ends_date = $this->_getTrialEndsDateForRestart();

        $subscription = \Stripe\Subscription::create(array(
            "customer" => $customer_stripe_id,
            "plan" => $stripe_plan_id,
            "trial_end" => $trial_ends_date,
        ));

        $userSubscription->stripe_id = $subscription->id;

        $userSubscription->save();
        $user->save();

        return redirect("/admin/user_details/{$userId}");

    }


    private function _getTrialEndsDateForRestart() {

// 4) For reactivation, the start date should be Tuesday if it is before midnight on Wednesday.
// If it is after midnight, it should be a week from Tuesday.
// The credit cards are processed at Midnight on Wednesdays, so this starts them the first week.

        date_default_timezone_set('America/Los_Angeles');

        // - must be UNIX timestamp

        //time of day cutoff for orders
        $cutOffTime = "16:00:00";
        $cutOffDay = "Wednesday";

        //change dates to WEDNESDAY
        //cutoff date is the last date to change or to signup for THIS week
        $cutOffFull = new \DateTime("this {$cutOffDay} {$cutOffTime}");
        $cutOffDate = new \DateTime("this {$cutOffDay}");

        //get today's date
        $now = new \DateTime('now');
        $today = new \DateTime('today');
        $triadEnds = (clone($cutOffDate))->modify('this tuesday');
        //echo "Today is " . $currentDay . "<br />";

        //echo "Cut off date: " . $cutOffDate->format('Y-m-d H:i:s') . "<br />";
        //echo "Current time: " . $todaysDate->format('Y-m-d H:i:s') . "<br />";

        //THIS IS ALL OLD CODE _ SINCE WE KNOW THE START DATE, we can just use that as the
        //check to see if today is the same day as the cutoff day
        if ($today == $cutOffDate) {

            //check to see if it's BEFORE the cutoff tine. If so, then this is a special case
            if ($now < $cutOffFull) {
                //ok, so it's the day of the cutoff, but before time has expired
                //SET the trial_ends date to $cutOffDate - no problem
                //echo "You have JUST beat the cutoff period <br /> Setting the trial_ends to today";

                //DO NOTHING
            } else {
                //the cutoff time has just ended
                //now, set the date to NEXT $cutOffDate
                //echo "You have missed the cutoff period <br /> Setting the trial_ends to next week";

                $triadEnds->modify("+1 week");
            }
        } else {
            //today is not the same as the trial ends date, so simply set the date to the next cutoff

            //DO NOTHING
        }

        return ($triadEnds->getTimestamp());

        //echo "Trial Ends: " . $trial_ends->format('Y-m-d H:i:s')  . "<br />";

        //echo "UNIX version of timestamp: " . $trial_ends->getTimestamp() . "<br />";

//			$TestDate = new DateTime('@1470463200');
//			$TestDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
        //echo "Converted back:" . $TestDate->format('Y-m-d H:i:s') . "<br />";

    }

}
