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
            ->select('users.id','users.email','users.name', 'users.start_date', 'subscriptions.status', DB::raw('sum(subinvoices.charge_amount/100) as revenue'))
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


		$upcomingSkipsNoMenu = App\Shippingholds::where('user_id',$id)
						->where('date_to_hold','>=',date('Y-m-d'))
						->whereNotIn('date_to_hold',$weeksMenusDates)
						->Orderby('date_to_hold')
						->get();
						
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
    
    
    	function pivotMenus($menusRaw) {
		//I TOTALLY cheat here. Each user should only have 3 menus, and I have sorted
		//the menu by user and menu id, so i know every third record is a new row.
		//the new collection is {[user_id, [_menunumber_:menu_id]]}		
			$menuPivotRow = new stdClass();
			$menus = [];
    		foreach ($menusRaw as $i => $menu) {
				$menuPivotRow->user_id = $menu->users_id;
				$menuPivotRow->menu[$i%3] = $menu->menus_id;
				if ($i%3 == 2) {
					array_push($menus,$menuPivotRow);
					$menuPivotRow = new stdClass();
				}
			}
			return $menus;
    	}
    
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

  
  		$menus = DB::table('menus_users')
				->select('delivery_date','menu_title','products.product_title','hasBeef','hasPoultry','hasFish','hasLamb','hasPork','hasShellfish',DB::raw('count(*) as total'))
	    		->join('menus','menus_users.menus_id','=','menus.id')
	    		->join('users','menus_users.users_id','=','users.id')
	    		->join('subscriptions','subscriptions.user_id','=','users.id')
	    		->join('products','subscriptions.product_id','=','products.id')
				->where('delivery_date', "=",$thisTuesday)
				->where('users.status', "<>","incomplete")
				->whereNotIn('users.id', $skipIdsThisWeek)
				->groupBy('delivery_date','menus_id','product_title')
				->orderBy('delivery_date')
				->orderBy('menus_id')
				->orderBy('products.id')
				->get();     
				
				
		$omnivoreMeals = App\WhatsCookings::where('week_of',$thisTuesday)
			->first()
			->getOmnivoreMeals()
			->orderBy('id')
			->get();
		$omnivoreMealsID = array_pluck($omnivoreMeals, 'id');
		
		$vegetarianMeals = App\WhatsCookings::where('week_of',$thisTuesday)
			->first()
			->getVegetarianMeals()
			->orderBy('id')
			->get();
		$vegetarianMealsID = array_pluck($vegetarianMeals, 'id');
		
		
		//get all the menus being sent out this week to active users
		$menusRaw = App\MenusUsers::where('delivery_date',$thisTuesday)
			->whereNotIn('users_id', $skipIdsThisWeek)
			->whereHas('users',function($q) {
				$q->whereIn('status',['active','inactive']);
			})
			->orderBy('users_id')
			->orderBy('menus_id')
			->get();

		//turn the new menus table into a laravel collection
		$menus = collect(pivotMenus($menusRaw));
		
		$total = $menus->count();
		//and if the id's are sorted correctly we should be ok. I am a little worried about it
		$standardOmnivore = $menus->where('menu',$omnivoreMealsID);
		$standardVegetarian = $menus->where('menu',$vegetarianMealsID);
		
		$omnivoreSubscribers = array_pluck($standardOmnivore,'user_id');
		$vegetarianSubscribers = array_pluck($standardVegetarian,'user_id');
		
		//now get everybody who didn't get a standard box...
		$everybodyElseRaw = App\MenusUsers::where('delivery_date',$thisTuesday)
			->whereNotIn('users_id', $skipIdsThisWeek)
			->whereNotIn('users_id', array_merge($omnivoreSubscribers,$vegetarianSubscribers))
			->whereHas('users',function($q) {
				$q->whereIn('status',['active','inactive']);
			})
			->orderBy('users_id')
			->orderBy('menus_id')
			->get();

		$everybodyElse = pivotMenus($everybodyElseRaw);
		$everybodyElseIds = array_pluck($everybodyElse,'user_id');
		$everybodyElseMenus = array_pluck($everybodyElse,'menu');
		sort($everybodyElseMenus);
		//...and and remove duplicate values from the array
		$everybodyElseMenus = array_values (array_unique($everybodyElseMenus, SORT_REGULAR));
		
		$thisWeeksStandardOmnivore = App\Product::
			whereHas('subscriptions',function($q) use ($omnivoreSubscribers) {
				$q->whereIn('user_id',$omnivoreSubscribers);
			})
			->get();
		
		
		$thisWeeksStandardOmnivore = DB::table('products')
				->select('products.product_title',DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.product_id','=','products.id')
	    		->join('menus_users','menus_users.users_id','=','subscriptions.user_id')
				->whereIn('subscriptions.user_id', $omnivoreSubscribers)
				->whereIn('menus_users.menus_id',$omnivoreMealsID)
				->groupBy('product_title')
				->orderBy('product_title')
				->get();   
		$thisWeeksStandardOmnivoreMenus = Menus::whereIn('id',$omnivoreMealsID)->get();
		$standardOmnivoreBoxes = new stdClass();
		$standardOmnivoreBoxes -> counts = $thisWeeksStandardOmnivore;
		$standardOmnivoreBoxes -> names = Menus::whereIn('id',$omnivoreMealsID)->get()->pluck('menu_title');
		
				
		$thisWeeksStandardVegetarian = DB::table('products')
				->select('products.product_title',DB::raw('count(*) as total'))
	    		->join('subscriptions','subscriptions.product_id','=','products.id')
	    		->join('menus_users','menus_users.users_id','=','subscriptions.user_id')
				->whereIn('subscriptions.user_id', $vegetarianSubscribers)
				->whereIn('menus_users.menus_id',$vegetarianMealsID)
				->groupBy('product_title')
				->orderBy('product_title')
				->get();    
		$thisWeeksStandardVegetarianMenus = Menus::whereIn('id',$vegetarianMealsID)->get();
		$vegetarianBoxes = new stdClass();
		$vegetarianBoxes -> counts = $thisWeeksStandardVegetarian;
		$vegetarianBoxes -> names = Menus::whereIn('id',$vegetarianMealsID)->get()->pluck('menu_title');
		
		$otherBoxCounts = [];
		$otherBoxes = [];	
		foreach ($everybodyElseMenus as $i=>$everybodyElseMenu)	 {
			$otherBoxCounts[$i] = DB::table('products')
					->select('products.product_title',DB::raw('count(*) as total'))
	    			->join('subscriptions','subscriptions.product_id','=','products.id')
	    			->join('menus_users','menus_users.users_id','=','subscriptions.user_id')
					->whereIn('subscriptions.user_id', $everybodyElseIds)
					->whereIn('menus_users.menus_id', $everybodyElseMenus[$i])
					->where('menus_users.delivery_date',$thisTuesday)
					->groupBy('product_title')
					->orderBy('product_title')
					->get();
			$boxScore = new stdClass();
			$boxScore -> counts = $otherBoxCounts[$i];
			$boxScore -> names = Menus::whereIn('id',$everybodyElseMenus[$i])->get()->pluck('menu_title');
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
    			->with(['menus'=>$menus
    				,'oldDate'=>''
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
