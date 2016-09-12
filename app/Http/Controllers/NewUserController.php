<?php

namespace App\Http\Controllers;

use App\AC_Mediator;
use App\Events\UserHasRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\User;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;
use App\Referral;
use App\ZipcodeStates;
use App\MenusUsers;
use App\Plan_change;
use Hash;
use Mail;
use DateTime;
use DateTimeZone;
use Session;
use Auth;

class NewUserController extends Controller
{

    const EXISTING_USER_CHILD_DISCOUNT = 1.51;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

	public function DisplayUserForm () {
		return view('register-1');
	}
	
	public function RecordNewuser (Request $request) {

//	    var_dump($request->email);
        $request->session()->put('has_registered', 'false');

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && '' == $existingUser->password) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|max:1000',
                'lastname' => 'required|max:1000',
                'email' => 'required|email|max:1000',
                'password' => 'required|max:255|same:password_confirmation',
                'zip' => 'required|digits:5',

            ]);
        } elseif($existingUser && !$existingUser->IsSubscribed()) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|max:1000',
                'lastname' => 'required|max:1000',
                'email' => 'required|email|max:1000',
                'password' => 'required|max:255|same:password_confirmation',
                'zip' => 'required|digits:5',

            ]);
        } else {
            $validator = Validator::make($request->all(), [
            	'firstname' => 'required|max:1000',
                'lastname' => 'required|max:1000',
			    'email' => 'required|email|max:1000|unique:users',
                'password' => 'required|max:255|same:password_confirmation',
                'zip' => 'required|digits:5',

            ]);
        }

		//add name to this
		
		//create a new user w form data from 
		//validation

		//make sure zipcode is on the approved list
		if ($request->zip != '' && !ZipcodeStates::where('zipcode',$request->zip)->first()) {
			return view('register.badzip')->with(['data' => $request, ]);
        }


		if ($validator->fails()) {
		        return redirect('/register')
		            ->withInput()
		            ->withErrors($validator);
		}

		if($existingUser) {
            $user = $existingUser;

            $request->session()->put('firstname', $user->first_name);
            $request->session()->put('lastname', $user->last_name);
            $request->session()->put('address', $user->billing_address);
            $request->session()->put('address2', $user->billing_address_line_2);
            $request->session()->put('state', $user->billing_state);
            $request->session()->put('zip', $request->zip);
            $request->session()->put('city', $user->billing_city);
            $request->session()->put('phone', $user->phone);

            $request->session()->put('existingUser', true);

        } else {
            $user = new User;
            $user->first_name = $request->firstname;
            $user->last_name = $request->lastname;
            $user->billing_zip = $request->zip;

            $request->session()->put('existingUser', false);
        }

		$user->email = $request->email;
		$user->password = Hash::make($request->password);
        $user->status = 'incomplete';
		$user->save();

		$productAdult = Product::where('sku','0202000000')->first();
		$productFamily1 = Product::where('sku','0202010000')->first();
    
        $adultDiscount = 0;
        $familyDiscount = 0;

		$request->session()->put('step1', true);
		$request->session()->put('firstname', $request->firstname);
        $request->session()->put('lastname', $request->lastname);
		$request->session()->put('user_id', $user->id);
		$request->session()->put('zip', $request->zip);
		$request->session()->put('adult_price', $productAdult->cost - $adultDiscount);
		$request->session()->put('family1_price', $productFamily1->cost - $familyDiscount);

		return Redirect::route('register.select_plan', array('user' => $user, 'zip' => $request->zip));
		
	}
	
	public function RecordPlan (Request $request) {
		
		//send the plan data straight to the view
		$numChildren = $request->children;
		$user = User::find($request->user_id);
		
		//calculate earliest start date
		$today = date('Y-m-d H:i:s'); 
		$nextTuesday = strtotime('next tuesday');
		$dateCompare = strtotime($today);
		
		if (($nextTuesday-$dateCompare)/86400 < 5) { //if today's date is less than 5 days from next tuesday, go to the tuesday after
			$startDate = date('Y-m-d', strtotime('+1 week', $nextTuesday));
			$endDate =  date('Y-m-d', strtotime('+6 weeks', $nextTuesday));
		} else {
			$startDate = date('Y-m-d', $nextTuesday);
			$endDate =  date('Y-m-d', strtotime('+5 weeks', $nextTuesday));
		}

    	$upcomingDates = [];

    	for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime('+1 day', $i)) {
			if (date('N', $i) == 2) {//Tuesday == 2 {
				$upcomingDates[date('m/d/y', $i)] = date('F j, Y', strtotime(date('m/d/y', $i)));
			}   
    	}
    	$request->session()->put('step2', true);
    	$request->session()->put('children', $numChildren);
    	$request->session()->put('upcoming_dates', $upcomingDates);
    	$request->session()->put('zip', $request->zip);

		return view('register.preferences')->with([
			'children'=>$numChildren,
			'user'=>$user,
			'zip'=>$request->zip,
			'upcomingDates'=>$upcomingDates
		]);
		
	}
	
	public function RecordPlanPreferences (Request $request) {
		
		/*sku decoder - 
		01 veg/onmivore
		02	num adults
		03	num children (04= 4 children)
		01  Gluten Free
		00	unused
		*/
		
		$plan_type = $request->plan_type;
		
		$num_kids = $request->children;	
		
		if ($plan_type=='Vegetarian Box') {
			$theSKU = "01";
			$plantype = 'Vegetarian';
			$request->session()->put('veg', 'checked');
			$request->session()->put('omni', '');
		}
		if ($plan_type=='Omnivore Box') {
			$theSKU = "02";
			$plantype = 'Omnivore';
			$request->session()->put('omni', 'checked');
			$request->session()->put('veg', '');
		}
		
		
		//num adults defaults to 02
		$theSKU .= "02";
	
			
		if ($num_kids=="0") {$theSKU .= "00";}
		if ($num_kids=="1") {$theSKU .= "01";}
		if ($num_kids=="2") {$theSKU .= "02";}
		if ($num_kids=="3") {$theSKU .= "03";}
		if ($num_kids=="4") {$theSKU .= "04";}
			
		
		//if ($request->glutenfree) {
		if ($request->prefs && in_array('9', $request->prefs)) {
			$theSKU .= "0100";
			$glutenfree = 'yes';
		}else{
			$theSKU .= "0000";
			$glutenfree = 'no';
		}
		
		
		//look up the product ID
		$newProduct = Product::where('sku',$theSKU)->first();
		
		$userSubscription = new UserSubscription;
		$userSubscription->user_id = $request->user_id;
		$userSubscription->product_id = $newProduct->id;
		if ($request->prefs) {$userSubscription->dietary_preferences = implode(",",$request->prefs);}
		$userSubscription->quantity=1;
		$userSubscription->stripe_plan=0;
		$userSubscription->stripe_id=0;
		$userSubscription->save();

		$prefs = array();
		if ($request->prefs && in_array('9', $request->prefs)) {
			array_push($prefs, 'gluten free');
			$request->session()->put('glutenfree', 'checked');
		} else {
			$request->session()->put('glutenfree', '');
		}
		if ($request->prefs && in_array('1', $request->prefs)) {
			array_push($prefs, 'red meat');
			$request->session()->put('redmeat', 'checked');
		} else {
			$request->session()->put('redmeat', '');
		}
		if ($request->prefs && in_array('2', $request->prefs)) {
			array_push($prefs, 'poultry');
			$request->session()->put('poultry', 'checked');
		} else {
			$request->session()->put('poultry', '');
		}
		if ($request->prefs && in_array('3', $request->prefs)) {
			array_push($prefs, 'fish');
			$request->session()->put('fish', 'checked');
		} else {
			$request->session()->put('fish', '');
		}
		if ($request->prefs && in_array('4', $request->prefs)) {
			array_push($prefs, 'lamb');
			$request->session()->put('lamb', 'checked');
		} else {
			$request->session()->put('lamb', '');
		}
		if ($request->prefs && in_array('5', $request->prefs)) {
			array_push($prefs, 'pork');
			$request->session()->put('pork', 'checked');
		} else {
			$request->session()->put('pork', '');
		}
		if ($request->prefs && in_array('6', $request->prefs)) {
			array_push($prefs, 'shellfish');
			$request->session()->put('shellfish', 'checked');
		} else {
			$request->session()->put('shellfish', 'checked');
		}
		if ($request->prefs && in_array('7', $request->prefs)) {
			array_push($prefs, 'nuts');
			$request->session()->put('nuts', 'checked');
		} else {
			array_push($prefs, 'no nuts');
			$request->session()->put('nuts', '');
		}

		$dietprefs = implode(", ",$prefs);
		
		//update STRIPE
		/*
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	
		$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
		$subscription->plan = $newProduct->stripe_plan_id;
		$subscription->save();
		*/
		$numChildren = $request->children;
		$user = User::find($request->user_id);
		
		//get the state for the zip code entered at the start of registration
		$state = ZipcodeStates::where('zipcode',$request->zip)->first();

		$menus_id = $request->menus_id;

		foreach ($menus_id as $ddate => $menu) {
			$i = 0;
			if (strtotime($ddate) >= strtotime($request->start_date)) {
				MenusUsers::where('users_id',$request->user_id)->where('delivery_date',date('Y-m-d', strtotime($ddate)))->delete();

				$menu_ids = (array)$menu;
				foreach ($menu_ids as $menu_id) {
			        $newMenu = new MenusUsers;
					$newMenu->users_id = $request->user_id;
					$newMenu->menus_id = $menu_id;
					$newMenu->delivery_date = $ddate;
					$newMenu->save();
			        $i++;
			        if ($i == 3) break;
			    }
			}
		}

		$request->session()->put('step3', true);
		$request->session()->put('plantype', $plan_type);
		$request->session()->put('zip', $request->zip);
		$request->session()->put('start_date', $request->start_date);
		$request->session()->put('dietprefs', $request->dietprefs);
		$request->session()->put('state', $state->state);

		return view('register.delivery')->with(['user'=>$user])->with(['zip'=>$request->zip]);
	}


	public function RecordDeliveryPreferences (Request $request) {
		
			if ($request->session()->get('has_registered') == "true") {
				
				//redirect them to the account page
				return redirect('/account/' . $request->user_id); 
				
			}
		
		//store first and last name in User field
		$user = User::find($request->user_id);
		$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
		$product = Product::where('id',$userSubscription->product_id)->first();
//var_dump($product);
		//store shipping address
		$shippingAddress = new Shipping_address;
		$shippingAddress->shipping_first_name = $request->firstname;
		$shippingAddress->shipping_last_name = $request->lastname;
		$user->first_name = $request->firstname;
		$user->last_name = $request->lastname;
		$shippingAddress->delivery_instructions = $request->delivery_instructions;
		$shippingAddress->shipping_address = $request->address;
		$shippingAddress->shipping_address_2 = $request->address_line_2;
		$shippingAddress->shipping_city = $request->city;
		$shippingAddress->shipping_state = $request->state;
		$shippingAddress->shipping_zip = $request->zip;
		$shippingAddress->user_id = $request->user_id;
		$shippingAddress->phone1 = $request->phone;
		$shippingAddress->shipping_country = "US";
		$shippingAddress->is_current = 1;
		$shippingAddress->address_type = $request->delivery_loc;
		

		$user->name = $request->firstname . " " . $request->lastname;
		
		//add - home/business
		//add - delivery instructions

		$shippingAddress->save();
		$user->save();
		//$numChildren = $request->children;
		
		//store children's birthdays
		//add - children's birthdays
		
		//take them to the next step!

		$request->session()->put('step4', true);
		$request->session()->put('delivery_loc', $request->delivery_loc);
		$request->session()->put('firstname', $request->firstname);
		$request->session()->put('lastname', $request->lastname);
		$request->session()->put('address', $request->address);
		$request->session()->put('address2', $request->address_line_2);
		$request->session()->put('state', $request->state);
		$request->session()->put('zip', $request->zip);
		$request->session()->put('city', $request->city);
		$request->session()->put('phone', $request->phone);
		$request->session()->put('instructions', $request->delivery_instructions);
		
		return view('register.payment')->
			with([
				'user'=>$user,
				'start_date'=>$request->start_date,
				'product'=>$product,
                'prefilledCoupon' => '', //$request->session()->get('existingUser') ? @$this->_existingCoupons[$product->sku] : '',
				]);
		
	}

	private $_existingCoupons = [
        '0202000000'	=> '',
        '0202010000' => 'Loyalty1Child-xj89',
        '0202020000' => 'Loyalty2Children-hg67',
        '0202030000' => 'Loyalty3Children-yj93',
        '0202040000' => 'Loyalty4Children-nb09',
        '0202000100' => 'LoyaltyGF-rp45',
        '0202010100' => 'Loyalty1ChildGF-jm83',
        '0202020100' => 'Loyalty2ChildrenGF-qw10',
        '0202030100' => 'Loyalty3ChildrenGF-xm72',
        '0202040100' => 'Loyalty4ChildrenGF-vs21',

        '0102000000' => '',
        '0102010000' => 'Loyalty1Child-xj89',
        '0102020000' => 'Loyalty2Children-hg67',
        '0102030000' => 'Loyalty3Children-yj93',
        '0102040000' => 'Loyalty4Children-nb09',
        '0102000100' => 'LoyaltyGF-rp45',
        '0102010100' => 'Loyalty1ChildGF-jm83',
        '0102020100' => 'Loyalty2ChildrenGF-qw10',
        '0102030100' => 'Loyalty3ChildrenGF-xm72',
        '0102040100' => 'Loyalty4ChildrenGF-vs21',
    ];


	public function RecordPayment (Request $request) {
		
		
			if ($request->session()->get('has_registered') == "true") {
				
				//redirect them to the account page
				return redirect('/account/' . $request->user_id); 
				
			}
		
			//engage STRIPE
			//check Stripe first, before anything happens, so user can be returned to the right place
				
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			
		
			$user = User::find($request->user_id);
			$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->first();

			//figure out date logic for trial period - 
			// - mist be UNIX timestamp
			
			//get today's date
			$todaysDate = new DateTime();
			
		
			//check to see what start date they want
			$startDate = date('Y-m-d', strtotime($request->start_date));
		
			//get the trial_ends date
			$trial_ends_timestamp = $this->GetTrialEndsDate_withStart($startDate);
			
			//check to see if there's a coupon
			$promo_type = $request->promotype;
			$promo_code = $request->promocode;
			$valid_coupon = 0;
			
			if ($promo_type == 'coupon') {
				
				//sweet, user has entered a coupon!
				if ($promo_code != '') {
					
					try {
					  //try for the coupon
						$coupon_obj = \Stripe\Coupon::retrieve($promo_code);
						$valid_coupon = 1;
						
					}
					catch (\Stripe\Error\InvalidRequest $e) {
						// Invalid parameters were supplied to Stripe's API
						// coupon is no good
						$valid_coupon = 0;
					
						
					} catch (Exception $e) {
					  
					}
					
					
					
				}
				
			}
			
			//if there is, see if it's a valid coupon
			
			//if it's valid, then apply it
			
			
			// Get the credit card details submitted by the form
			$token = $request->stripeToken;
			
			//user does not have a oupon
			if ($valid_coupon==0) {
				
					try {
						$customer = \Stripe\Customer::create(array(
								"source" => $token,
								"plan" => $userProduct->stripe_plan_id,
								"email" => $user->email,
								"trial_end" => $trial_ends_timestamp
							));

					} catch (\Stripe\Error\Card $e) {
					    
						// Card was declined.
						$e_json = $e->getJsonBody();
						$err = $e_json['error'];
						$stripeError = $err['message'];
						
							//take user back to payment page
							return view('register.payment')->
									with([
										'user'=>$user,
										'start_date'=>$request->start_date,
										'product'=>$userProduct,
						                'prefilledCoupon' => $request->session()->get('existingUser') ? @$this->_existingCoupons[$userProduct->sku] : '',
										'stripeError' => $stripeError,
										]);
						
					} catch (\Stripe\Error\ApiConnection $e) {
					    // Network problem, perhaps try again.
					} catch (\Stripe\Error\InvalidRequest $e) {
					    // You screwed up in your programming. Shouldn't happen!
					} catch (\Stripe\Error\Api $e) {
					    // Stripe's servers are down!
					} catch (\Stripe\Error\Base $e) {
					    // Something else that's not the customer's fault.
					}
		
			
			} //end if
			
			//user has a valid coupon
			if ($valid_coupon==1) {
				
				try {
					$customer = \Stripe\Customer::create(array(
						"source" => $token,
						"plan" => $userProduct->stripe_plan_id,
						"email" => $user->email,
						"trial_end" => $trial_ends_timestamp,
						"coupon" => $promo_code
					));
					
				} catch (\Stripe\Error\Card $e) {
					    
					// Card was declined.
					$e_json = $e->getJsonBody();
					$err = $e_json['error'];
					$stripeError = $err['message'];
					
						//take user back to payment page
						return view('register.payment')->
								with([
									'user'=>$user,
									'start_date'=>$request->start_date,
									'product'=>$userProduct,
					                'prefilledCoupon' => $request->session()->get('existingUser') ? @$this->_existingCoupons[$userProduct->sku] : '',
									'stripeError' => $stripeError,
									]);
									
					} catch (\Stripe\Error\ApiConnection $e) {
					    // Network problem, perhaps try again.
					} catch (\Stripe\Error\InvalidRequest $e) {
					    // You screwed up in your programming. Shouldn't happen!
					} catch (\Stripe\Error\Api $e) {
					    // Stripe's servers are down!
					} catch (\Stripe\Error\Base $e) {
					    // Something else that's not the customer's fault.
					}


			
			
			}
			
			
						
			$user->stripe_id = $customer->id;
			
			//update User with card_last_four and card_type
			if (isset($customer->sources->data[0]->last4)) {
				$user->card_last_four = $customer->sources->data[0]->last4;
			}
			if (isset($customer->sources->data[0]->brand)) {
				$user->card_brand = $customer->sources->data[0]->brand;
			}
			
			//get the subscription ID
			$userSubscription->stripe_id = $customer->subscriptions->data[0]->id;
			$userSubscription->status= "active";
			
			//update statuses to "active"
			//return errors if CC didn't go through
			
			//record billing address
			$user->billing_address = $request->address;
			$user->billing_address_line_2 = $request->address_2;
			$user->billing_city =  $request->city;
			$user->billing_state =  $request->state;
			$user->billing_zip =  $request->zip;
			$user->billing_country = "US";
			$user->start_date =  date('Y-m-d', strtotime($request->start_date));
			$user->phone =  $request->phone;
			$userSubscription->name=$user->name;
			
			$userSubscription->save();

            $user->status = 'active';
            $user->save();

			$firstDelivery = MenusUsers::where('users_id',$request->user_id)->where('delivery_date',date('Y-m-d', strtotime($request->start_date)))->get();
			if (count($firstDelivery) > 0) {
				$meal1 = $firstDelivery[0]->menus_id;
				$meal2 = $firstDelivery[1]->menus_id;
				$meal3 = $firstDelivery[2]->menus_id;
			} else {
				$meal1 = '0';
				$meal2 = '0';
				$meal3 = '0';
			}

		$request->session()->flush();

        event(new UserHasRegistered($user));
        Auth::login($user, true);

		$request->session()->put('has_registered','true');
		
        return view('register.congrats')->with([
        	'user'=>$user,
        	'start_date'=>$request->start_date,
        	'meal1'=>$meal1,
        	'meal2'=>$meal2,
        	'meal3'=>$meal3,
        ]);
		
	}


    public function SubscribeToWaitingList(Request $request) {
        $ac = AC_Mediator::GetInstance();

        try {
            $ac->SubscribeToWaitingList($request->email, $request->firstname, $request->lastname, $request->zip);
        } catch (\Exception $e) {

        }

        return view("register.waiting_list_thanks");
    }


	//checks validity of a coupon and returns the new price and the discount amount
	public function CheckCoupon ($price, $coupon_code) {
		
		
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		
		try {
		  //try for the coupon
			$coupon_obj = \Stripe\Coupon::retrieve($coupon_code);
			$valid_coupon = 1;
			
			if ($coupon_obj->amount_off ) {
				
					$amount_off = $coupon_obj->amount_off/100;
					$new_price = $price - $amount_off;
					$returnJSON = '{"status":"valid","discount": "' .  $amount_off . '","newprice":"' . $new_price . '"}';
					
			}
			
			if ($coupon_obj->percent_off ) {
				
					$percent_off = $coupon_obj->percent_off/100;
					$amount_off = round($price * $percent_off,2);
					$new_price = $price - $amount_off;
					
					$returnJSON = '{"status":"valid","discount": "' .  $amount_off . '","newprice":"' . $new_price . '"}';
				
				
			}
			
			
		
		}
		catch (\Stripe\Error\InvalidRequest $e) {
			// Invalid parameters were supplied to Stripe's API
			// coupon is no good
			$valid_coupon = 0;
			$returnJSON = '{"status":"invalid","discount": "0","newprice":"' . $price . '"}';
			
			
		} catch (Exception $e) {
		  
		}
		
		//return either the discounted price OR "invalid coupon"
		return $returnJSON;
		
	}
	
	
	public function GetTrialEndsDate_withStart ($startDate) {
		
		//trial ends should be the PREVIOUS Wendesday at 16:00:00 
		
		//time of day cutoff for orders
		$cutOffTime = "16:00:00";
		$cutOffDay = "Wednesday";
		
		
		$trial_end_date = new DateTime($startDate, new DateTimeZone('America/Los_Angeles'));
		$trial_end_date->modify('last ' . $cutOffDay . ' ' . $cutOffTime);
		
		return ($trial_end_date->getTimestamp());
		
	}
		
	public function GetTrialEndsDate() {
		
		
		
			//use start date - find the previous Wednesday at 16:00:00
		
		
		
		
			//figure out date logic for trial period - 
			// - mist be UNIX timestamp
			
			$trial_ends = "";
			
			//time of day cutoff for orders
			$cutOffTime = "16:00:00";
			$cutOffDay = "Wednesday";
			
			//change dates to WEDNESDAY
			//cutoff date is the last date to change or to signup for THIS week
			$cutOffDate = new DateTime();
			$cutOffDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			$cutOffDate->modify('this ' . $cutOffDay . ' ' . $cutOffTime);
		
			//get today's date
			$todaysDate = new DateTime();
			$todaysDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			$currentDay = date_format($todaysDate, "l");
			$currentTime = date_format($todaysDate, "H:is");
			
			//echo "Today is " . $currentDay . "<br />";
			
			//echo "Cut off date: " . $cutOffDate->format('Y-m-d H:i:s') . "<br />";
			//echo "Current time: " . $todaysDate->format('Y-m-d H:i:s') . "<br />";
			
			
			//THIS IS ALL OLD CODE _ SINCE WE KNOW THE START DATE, we can just use that as the 
			//check to see if today is the same day as the cutoff day
			if ($currentDay==$cutOffDay) {
				
				//check to see if it's BEFORE the cutoff tine. If so, then this is a special case
				if ($currentTime < $cutOffTime) {

					//ok, so it's the day of the cutoff, but before time has expired
					//SET the trial_ends date to $cutOffDate - no problem
					//echo "You have JUST beat the cutoff period <br /> Setting the trial_ends to today"; 
					$trial_ends = $cutOffDate;

				}else{

					//the cutoff tiem has just ended
					//now, set the date to NEXT $cutOffDate
					$trial_ends = new DateTime();
					$trial_ends->setTimeZone(new DateTimeZone('America/Los_Angeles'));
					$trial_ends->modify('next ' . $cutOffDay . ' ' . $cutOffTime);
					//echo "You have missed the cutoff period <br /> Setting the trial_ends to next week"; 
					

				}
			
			}else{
				
				//today is not the same as the trial ends date, so simply set the date to the next cutoff 
				$trial_ends = $cutOffDate;
				
			}
		
			
			return ($trial_ends->getTimestamp());
		
			//echo "Trial Ends: " . $trial_ends->format('Y-m-d H:i:s')  . "<br />";
			
			//echo "UNIX version of timestamp: " . $trial_ends->getTimestamp() . "<br />";
		
			$TestDate = new DateTime('@1470463200');
			$TestDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			//echo "Converted back:" . $TestDate->format('Y-m-d H:i:s') . "<br />";
			
	}
	
	public function ChangeRatePlan ($id, $num_children, $weekof) {
		
			//record $id, $num_children, $weekof in the plan_changes table with status "to_change"
			$plan_change = new Plan_change;
			$plan_change->user_id = $id;
			$plan_change->date_to_change = $weekof;
			$plan_change->status = "to_change";
			
			//look up the product ID

			$userSubscription = UserSubscription::where('user_id',$id)->first();
			$current_product_id = $userSubscription->product_id;

			//lookup the SKU

			$currentProduct = Product::where('id', $current_product_id)->first();
			$current_sku = $currentProduct->sku;
			
			$plan_change->old_sku = $current_sku;

			//update the sku - 
			$sku_array = str_split($current_sku, 2);

			//update the third position (2) to new number of children
			$sku_array[2] = "0" . $num_children;

			$new_sku = implode ("",$sku_array);
			
			$change_exists = Plan_change::where('user_id',$id)
										->where('date_to_change',$weekof)
										->where('status','to_change')
										->first();

			$plan_change->sku_to_change = $new_sku;
			
			if (count($change_exists) == 0) $plan_change->save();
			else {
				$change_exists->old_sku = $change_exists->sku_to_change;
				$change_exists->sku_to_change = $new_sku;
				$change_exists->save();
			}
					
			//return success code
			http_response_code(200);
			return redirect('/delivery-schedule'); 
			
	}



}