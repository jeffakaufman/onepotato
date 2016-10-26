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
        // last week is the last week we have "shipped" invoices for
        $lastPeriodEndDate = Subinvoice::max('ship_date');
        $lastPeriodEndDate = date('Y-m-d',strtotime($lastPeriodEndDate."+8 days"));
        
        $activeWeeks = DB::table('users')
            ->select('start_date', DB::raw('count(*) as newSubCount'))
            ->where('status','active')
            ->groupBy('start_date')
            ->orderBy('start_date','asc')
            ->get();
            
        $skips = DB::table('shippingholds')
            ->select('date_to_hold', DB::raw('count(*) as skips'))
            ->whereIn('hold_status',['released-after-hold','hold'])
            ->groupBy('date_to_hold')
            ->orderBy('date_to_hold','asc')
            ->get();
            
        $subs = [];
        $newSubs = [];
        

        $subs["Today"] = DB::table('users')
        	->select('status', DB::raw('count(*) as total'))
            ->where('status','<>','admin')
            ->where('created_at','>=',date('Y-m-d'))
            ->where('status','active')
            ->groupBy('status')
            ->get();
          
            
        $subs["Yesterday"] = DB::table('users')
        	->select('status', DB::raw('count(*) as total'))
            ->where('status','<>','admin')
            ->where('created_at','>=',date('Y-m-d',strtotime('-1 day')))
            ->where('created_at','<',date('Y-m-d'))
            ->groupBy('status')
            ->get();

    	$subs["Last Week"] = DB::table('users')
        	->select('status', DB::raw('count(*) as total'))
            ->where('status','<>','admin')
            ->where('created_at','>=',date('Y-m-d',strtotime('-7 day')))
            ->where('created_at','<',date('Y-m-d'))
            ->groupBy('status')
            ->get();

    	$subs["Last Month"] = DB::table('users')
        	->select('status', DB::raw('count(*) as total'))
            ->where('status','<>','admin')
            ->where('created_at','>=',date('Y-m-d',strtotime('-1 month')))
            ->where('created_at','<',date('Y-m-d'))
            ->groupBy('status')
            ->get();   
             
        $subs["Last Ninety"] = DB::table('users')
        	->select('status', DB::raw('count(*) as total'))
            ->where('status','<>','admin')
            ->where('created_at','>=',date('Y-m-d',strtotime('-90 days')))
            ->where('created_at','<',date('Y-m-d'))
            ->groupBy('status')
            ->get();   
         
//echo json_encode($subs["Today"]);die;     

        
        $weeklySummaries = [];
       	$week  = new stdClass;
       	$totalSubs = 0;
        foreach($activeWeeks as $activeWeek) {
        	$week->start_date = $activeWeek->start_date;
        	$week->newSubCount = $activeWeek->newSubCount;
        	$week->recurringSubCount = $totalSubs;
        	$totalSubs += $activeWeek->newSubCount;
        	$week->totalSubs = $totalSubs;
        	foreach ($skips as $skip) {
    			if (date('Y-m-d',strtotime($skip->date_to_hold)) == date('Y-m-d',strtotime($activeWeek->start_date))) { 
    				$week->skips = $skip->skips; 
    			} 
    		}
    		if(!isset($week->skips)){$week->skips = 0;}
    		if (strtotime($activeWeek->start_date) > strtotime('-30 days')) {
    			array_push($weeklySummaries,$week);
    		}
    		$week = new stdClass;
        }        

    	return view('admin.dashboard')
    			->with([
    				'weeklySummaries' => $weeklySummaries,
    				'subs' => $subs,
    				'newSubs' => $newSubs,
                ]);
	
    
    
    
    } 
     
     
    public function showUsers()
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

        /**
         * @var User $user
         */

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

		$deliveryHistory = App\Subinvoice::where('user_id',$id)
				->where('charge_date','>','2016-9-28')
				->orderBy('charge_date','desc')
				->get();


        if(isset($deliveryHistory[0])) {
            $oldMenus = $user->menus()
                ->where('delivery_date','<',date('Y-m-d',strtotime($deliveryHistory[0]->charge_date.'+1 week')))
                ->where('delivery_date','>','2016-10-4')
                ->orderBy('delivery_date','desc')
                ->get();
        } else {
            $oldMenus = [];
        }

		$oldDeliveries = new stdClass;
		$invoiceMenus = [] ;
		$oldDate = "";
		$invoiceIndex = 0;


		
		foreach ($oldMenus as $i => $oldMenu) {
			if ($oldMenu->pivot->delivery_date <> $oldDate) {
				$oldDeliveries->delivery_date = $oldMenu->pivot->delivery_date;
				$oldDeliveries->weekMenu[0] = $oldMenu->menu_title;
				$oldDeliveries->skipStatus = $user->getSkips()->where('date_to_hold',$oldDeliveries->delivery_date)->first();
				$oldDate = $oldMenu->pivot->delivery_date;
			} else {
				$oldDeliveries->weekMenu[$i%3] = $oldMenu->menu_title;
			}
			if ($i%3 == 2) {
				$deliveryHistory[$invoiceIndex]->menus = $oldDeliveries->weekMenu;
				$hold_status = isset($weekMenus->skipStatus->hold_status) ? $weekMenus->skipStatus->hold_status : "";
				$deliveryHistory[$invoiceIndex]->skipStatus = isset($oldDeliveries->skipStatus) ? $oldDeliveries->skipStatus : "";
				array_push($invoiceMenus,$oldDeliveries);
				$oldDeliveries = new stdClass();
				$invoiceIndex++;
			}
		}

		$credits = App\Credit::where('user_id',$id)->get();

        $lastCancelLinkSent = '';

        $lastCancelLinkRecord = App\CancelLink::where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if($lastCancelLinkRecord) {
            $linkSent = new \DateTime($lastCancelLinkRecord->created_at);
            $lastCancelLinkSent = $linkSent->format("m/d/Y @ H:i");
        }

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
						'deliveryHistory'=>$deliveryHistory,
                        'lastCancelLinkSent' => $lastCancelLinkSent,
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
        $lastPeriodEndDate = Subinvoice::max('ship_date');
        $lastPeriodEndDate = date('Y-m-d',strtotime($lastPeriodEndDate."+8 days"));
        
        
        
        $lastTuesday = date('Y-m-d',strtotime($lastPeriodEndDate.'-7 day'));         
        $thisTuesday = date('Y-m-d',strtotime($lastTuesday . '+7 days'));
        $nextTuesday = date('Y-m-d',strtotime($thisTuesday . '+7 days'));


        $reportsBuilder = new App\ReportsBuilder();
        $reportData = $reportsBuilder->GetWeeklyKitchenReport(new \DateTime($thisTuesday), new \DateTime($thisTuesday));

    	return view('admin.reports')
    			->with([
    				'thisTuesday' => date('F d', strtotime($thisTuesday)),
    				'reportData' => $reportData,
                ]);
	
    
    
    
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
        } else {
            $userSubscription->dietary_preferences = '';
        }

        $userSubscription->save();

        //make sure trial_ends is set the same -

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

        $user = User::find($userId);
        $logger = new App\SimpleLogger("ProductChanges.log");
        $logger->Log("#{$user->id} [{$user->email}] {$user->first_name} {$user->last_name} Product changed to #{$newProduct->id} {$newProduct->sku} {$newProduct->product_descritpion} \${$newProduct->cost} BY ADMIN");

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


    public function EditMenus($userId, $deliveryDate) {

        $menus = DB::table('menus')
            ->join('menus_whats_cookings', 'menus.id', '=', 'menus_whats_cookings.menus_id')
            ->join('whats_cookings', 'whats_cookings.id', '=', 'menus_whats_cookings.whats_cookings_id')
            ->leftJoin('menus_users', function($join) use($userId, $deliveryDate) {
                $join->on('menus.id', '=', 'menus_users.menus_id')
                    ->where('menus_users.users_id', '=', $userId)
                    ->where('menus_users.delivery_date', '=', $deliveryDate);
            })
            ->where('whats_cookings.week_of', '=', $deliveryDate)
            ->get(['menus.*', 'menus_users.id as mu_id']);
//var_dump($menus);die();

        $user = User::find($userId);
        return view("admin.users.parts.edit_menus")->with([
            "user" => $user,
            'deliveryDate' => $deliveryDate,
            'menus' => $menus,
        ]);
    }

    public function SaveMenus($userId, $deliveryDate) {
        $request = request();

        DB::table('menus_users')
            ->where('users_id', '=', $userId)
            ->where('delivery_date', '=', $deliveryDate)
            ->delete();

        $insertArray = [];
        foreach((array)$request->menu_id as $menuId) {
            $insertArray[] = [
                'menus_id' => $menuId,
                'users_id' => $userId,
                'delivery_date' => $deliveryDate,
            ];
        }
        DB::table("menus_users")->insert($insertArray);

        return redirect("/admin/user_details/{$userId}");
    }


    public function SkipDelivery($userId, $deliveryDate) {
        $dm = App\DeliveryManager::GetInstance();

        $response = new \stdClass();
        $response->ok = false;

        $dm->SkipDelivery(User::find($userId), new \DateTime($deliveryDate));
        $response->ok = true;

        return json_encode($response);
    }

    public function UnskipDelivery($userId, $deliveryDate) {
        $dm = App\DeliveryManager::GetInstance();

        $response = new \stdClass();
        $response->ok = false;

        $dm->UnskipDelivery(User::find($userId), new \DateTime($deliveryDate));
        $response->ok = true;

        return json_encode($response);
    }
}
