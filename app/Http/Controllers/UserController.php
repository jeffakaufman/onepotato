<?php

namespace App\Http\Controllers;

use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Auth;
use App\User;
use App\Subinvoice;
use App\Shippingholds;
use App\Shipping_address;
use App\Csr_note;
use App\Cancellation;
use App\UserSubscription;
use App\Dietary_preference;
use App\Order;
use App\Product;
use App\Referral;
use Mail;
use Hash;
use Auth;
use CountryState;
use App\WhatsCookings;
use App\Menus;
use App\MenusUsers;
use App\Plan_change;
use stdClass;
use Carbon\Carbon;
use DB;
use \ActiveCampaign;
use \App\Menu;
use App\AC_Mediator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Show the users
     *
     * @return Response
     */
    public function show()
    {
        return view('admin/users/user');
    }

	
 	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showUsers()
    {
        	//$users = User::get();
        	
        	//$users = User::has('userSubscription')->get();
        	/*
        	$users = User::whereHas('userSubscription', function ($query) {
        		$query->where('stripe_id', '<>', '');
        		$query->where('name', '<>', '');
        		})
        		->orderBy('start_date', 'desc')
        		->orderBy('name', 'asc')
        		->get();
        	*/
        	
        	
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
            'orderBy' => 'start_date',
            'orderDir' => 'desc',

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
/*
		<div class="user_name col-sm-2 text-center"><strong><a href="/admin/users/updateListParams/orderBy/status">Status</a></strong></div>

         */
    }

	//get data for the accounts page - current

	public function getAccount($id = null) {
		
		$id = !isset($id) ? Auth::id() : $id;
		$nextTuesday = strtotime('next tuesday');
		$today = date('Y-m-d H:i:s'); 
		$cancelMessage = "";
				
		//get all the user objects and pass to the view
		$user = User::find($id);

		$states = CountryState::getStates('US');
		$shippingAddress = Shipping_address::where('user_id',$id)->orderBy('is_current', 'desc')->first();
	
		$userSubscription = UserSubscription::where('user_id',$id)->first();

		if ($userSubscription) {
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->first();
		}

		$referrals = Referral::where('referrer_user_id',$id)->get();

		$invoices = Subinvoice::where('user_id',$id)
			->where('invoice_status','shipped')
			->orderBy('order_id','desc')
			->get();
		//removed from qyere--->whereDate('period_end_date','<=',Carbon::today()->toDateString())
		//check for any processed orders - the order was sent to shipping for the next week's delivery
		$currentInvoice = Subinvoice::where('user_id',$id)
			->where('invoice_status','sent_to_ship')
			->orderBy('charge_date','desc')
			->first();

		if (isset($currentInvoice->charge_date)) {
			$lastDeliveryDate = date('l, F jS',strtotime("next tuesday",strtotime($currentInvoice->charge_date)));
			if ( $lastDeliveryDate < $today ) {
				$cancelMessage = "Your final delivery is scheduled for ".$lastDeliveryDate." has already been processed and cannot be changed.";
			} 
		}


		
		$shipments = [];
		for ($i = 0; $i < count($invoices); $i++) {
			$deliveryHistory = new stdClass;
			$deliveryHistory->order_id = $invoices[$i]->order_id;
			$deliveryHistory->ship_date = date('F j, Y', strtotime($invoices[$i]->period_end_date."-1 day"));
			$deliveryHistory->cost = ($invoices[$i]->charge_amount)/100;
			$deliveryHistory->menus = MenusUsers::where('users_id',$id)->where('delivery_date', date('Y-m-d', strtotime($invoices[$i]->period_end_date."-1 day")) )->get();
			$shipments[] = $deliveryHistory;
		}
		//get the next delivery their change will apply
		$today = date('N');
		if		($today == 1)	{ $changeDate = date('l, F jS', strtotime("+8 days"));  }
		elseif	($today == 2)	{ $changeDate = date('l, F jS', strtotime("+7 days"));  }
		elseif	($today == 3)	{ $changeDate = date('l, F jS', strtotime("+6 days"));  }
		elseif	($today == 4)	{ $changeDate = date('l, F jS', strtotime("+12 days")); }
		elseif	($today == 5)	{ $changeDate = date('l, F jS', strtotime("+11 days")); }
		elseif	($today == 6)	{ $changeDate = date('l, F jS', strtotime("+10 days")); }
		elseif	($today == 7)	{ $changeDate = date('l, F jS', strtotime("+9 days"));  }
		
		return view('account')
					->with(['user'=>$user, 
							'shippingAddress'=>$shippingAddress, 
							'userSubscription'=>$userSubscription, 
							'userProduct'=>$userProduct, 
							'states'=>$states,
							'referrals'=>$referrals,
							'shipments'=>$shipments,
							'changeDate'=>$changeDate,
							'cancelMessage'=>$cancelMessage]);
	}	

	//handles all the /account/ functionality - current
	
	public function editAccount(Request $request) {
		
			//get all the user objects and pass to the view
			$user = User::find($request->user_id);
			$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
			$states = CountryState::getStates('US');
			$shippingAddress = Shipping_address::where('user_id',$request->user_id)->orderBy('is_current', 'desc')->first();
			

			if ($userSubscription) {
				$productID = $userSubscription->product_id;
				$userProduct = Product::where('id',$productID)->first();
			}

			$update_type = $request->update_type;
			
			if ($update_type=="referrals") {
				
					$user = User::find($request->user_id);

					//validate form
					/*
					$validator = Validator::make($request->all(), [
				        'send_email' => 'required|max:255|email'
				    ]);

				    if ($validator->fails()) {
				        return redirect('/user/referrals/' . $id)
				            ->withInput()
				            ->withErrors($validator);
				    }
					*/

					//send email
					$to_send = $request->email;
					$custom_message = $request->message;
					$friendname = $request->name;

					//record a new referral in the referral table
					$referral = new Referral;
					$referral->friend_name = $friendname;
					$referral->referral_email = $to_send;
					$referral->did_subscribe = 0;
					$referral->referrer_user_id = $request->user_id;


					$referral->save();


					$data = [
					           'friendname' => $user->name,
					           'custommessage' => $custom_message,
							   'to_send' => $to_send,
							   'friendid' => $request->user_id,
							   'referralid' => $referral->id
					];

					Mail::send('emailtest', $data , function($message) use ($to_send)
					{
						    $message->from('mattkirkpatrick@gmail.com');
						    $message->to($to_send, '')->subject('A Message from a Friend at One Potato!');
					});
				
				
			}
			
			if ($update_type=="delivery_address") {
				
				$shippingAddress->shipping_address = $request->address1;
				$shippingAddress->shipping_address_2 = $request->address2;
				$shippingAddress->shipping_city = $request->city;
				$shippingAddress->shipping_state = $request->state;
				$shippingAddress->shipping_zip = $request->zip;
				$shippingAddress->phone1 = $request->phone;
				$shippingAddress->delivery_instructions = $request->delivery_instructions;
				$shippingAddress->save();
				
			}
			
			if ($update_type=="account") {

				$user->name = $request->first_name . " " . $request->last_name;
				$user->first_name = $request->first_name;
				$user->last_name = $request->last_name;
				$user->email = $request->email;
				if ($request->password != "") {
					$user->password = Hash::make($request->password);
				}
				$user->save();

			}
			
			if ($update_type=="payment") {
				
				//figure out which plan the user is currently subscribed to
				\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

				// Get the credit card details submitted by the form
				$token = $request->stripeToken;
				

				//first see if there's already a record for this customer in STRIPE and update using Token
				$customer = \Stripe\Customer::retrieve($user->stripe_id);
				$customer->source = $token; // obtained with Stripe.js
				$customer->email = $user->email;
				$customer->plan = $userProduct->stripe_plan_id;
				$customer->save();
				
				//update User with card_last_four and card_type
				$user->card_last_four = $customer->sources->data[0]->last4;
				$user->card_brand = $customer->sources->data[0]->brand;
				$user->save();
				
			}
			
			
			if ($update_type=="meals") {
			/*sku decoder - 
			01 veg/onmivore
			02	num adults
			03	num children (04= 4 children)
			01  Gluten Free (00 is regular with Gluten)
			00	unused
			*/

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
				if ($num_kids=="0") {$theSKU .= "04";}

			}else{
				$theSKU .= "00";
			}
			
			if ($gluten_free == "1") {
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

			//update STRIPE
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id);
			$subscription->plan = $newProduct->stripe_plan_id;
			$subscription->save();

			}
			//end meal/plan update
			
			$referrals = Referral::where('referrer_user_id',$request->user_id)->get();
			
			
			return redirect('/account/' . $request->user_id);
			/*
			return view('account')
						->with(['user'=>$user, 
								'shippingAddress'=>$shippingAddress, 
								'userSubscription'=>$userSubscription, 
								'userProduct'=>$userProduct, 
								'states'=>$states,
								'referrals'=>$referrals]);
								*/
	}

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showUser($id)
    {

		$user = User::find($id);
		$states = CountryState::getStates('US');
		$shippingAddresses = Shipping_address::where('user_id',$id)->orderBy('is_current', 'desc')->get();
		$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();
		
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		
		if ($userSubscription) {
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->firstOrFail();
		}
		
		$referrals = Referral::where('referrer_user_id',$id)->get();
		
		return view('admin.users.user')
				->with(['user'=>$user, 
						'shippingAddresses'=>$shippingAddresses, 
						'userSubscription'=>$userSubscription, 
						'csr_notes'=>$csr_notes,
						'userProduct'=>$userProduct, 
						'states'=>$states,
						'referrals'=>$referrals]);

    }

    public function showUserDetails($id)
    {

		$user = User::find($id);
		$states = CountryState::getStates('US');
		$shippingAddresses = Shipping_address::where('user_id',$id)->orderBy('is_current', 'desc')->get();
		$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();

		$userSubscription = UserSubscription::where('user_id',$id)->first();

		if ($userSubscription) {
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->firstOrFail();
		}

		$referrals = Referral::where('referrer_user_id',$id)->get();

		return view('admin.users.user_details')
				->with(['user'=>$user,
						'shippingAddresses'=>$shippingAddresses,
						'userSubscription'=>$userSubscription,
						'csr_notes'=>$csr_notes,
						'userProduct'=>$userProduct,
						'states'=>$states,
						'referrals'=>$referrals]);

    }

	public function newUser() {
		
		
		return view('admin.users.user-new');
		
	}
	
	public function createUser(Request $request) {
		
		//validation
		$validator = Validator::make($request->all(), [
	        'user_name' => 'required|max:255',
		    'user_email' => 'required|max:1000',
			'user_password' => 'required|max:255'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/user/new')
	            ->withInput()
	            ->withErrors($validator);
	    }

	    $user = new User;
	    $user->name = $request->user_name;
		$user->email = $request->user_email;
		$user->password = $request->user_password;
		$user->save();
		
		return view('admin.users.user-new-payment')->with(['user'=>$user]);
		
		
		
	}
	
	public function testCreateUserPayment ($id, Request $request) {
		
		echo "help";
		
	}
	
	public function createUserPayment($id, Request $request) {
		
			$user = User::find($id);
			//$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->firstOrFail();
			
			//figure out which plan the user is currently subscribed to
			
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			
			
			// Get the credit card details submitted by the form
			$token = $request->stripeToken;
			
			echo $token;
			echo $user->email;
			
			
			$customer = \Stripe\Customer::create(array(
					  "source" => $token,
					  "plan" => 'account_only',
					  "email" => $user->email)
			);
			
						
			$user->stripe_id = $customer->id;
			
			
				
			$user->save();
			
			//create a record in subscriptions table to store the subscription id
				
			//return "test";
			return view('admin/users/user-new-plan')->with(['user'=>$user]);
			
			//NOTE: When a user changes their plan, need to ALSO update that in STRIPE
		
		
	}
	
	public function createUserSubscription($id, Request $request) {
		
		$validator = Validator::make($request->all(), [
	        'plan_type' => 'required|max:255',
		    'plan_size' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/user/subscriptions/' . $id)
	            ->withInput()
	            ->withErrors($validator);
	    }
	
		/*sku decoder - 
		01 veg/onmivore
		02	num adults
		03	num children (04= 4 children)
		01  Gluten Free (00 - regular)
		00	unused
		*/
		
		$plan_type = $request->plan_type;
		$plan_size = $request->plan_size;
		$num_kids = $request->num_children;
		$theSKU = '';
		
		
		if ($plan_type=='vegetarian') {
			$theSKU = "01";
		}
		if ($plan_type=='omnivore') {
			$theSKU = "02";
		}
		
		
		//num adults defaults to 02
		$theSKU .= "02";
		
		if ($plan_size=="family") {
			
			if ($num_kids=="0") {$theSKU .= "00";}
			if ($num_kids=="1") {$theSKU .= "01";}
			if ($num_kids=="2") {$theSKU .= "02";}
			if ($num_kids=="3") {$theSKU .= "03";}
			if ($num_kids=="0") {$theSKU .= "04";}
			
		}else{
			$theSKU .= "00";
		}
		
		$theSKU .= "0000";
		
	
		
		//look up the product ID
		$newProduct = Product::where('sku',$theSKU)->firstOrFail();
		
		$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
		$userSubscription->product_id = $newProduct->id;
		$userSubscription->dietary_preferences = implode(",",$request->prefs);
		$userSubscription->save();
		
		//update STRIPE
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	
		$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
		$subscription->plan = $newProduct->stripe_plan_id;
		$subscription->save();
		
		
	}
	

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function createUser_old (Request $request)
    {

		$validator = Validator::make($request->all(), [
	        'user_name' => 'required|max:255',
		    'user_email' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/user/' . $id)
	            ->withInput()
	            ->withErrors($validator);
	    }

	    $user = new User;
	    $user->name = $request->user_name;
		$user->email = $request->user_email;
		$user->billing_address = $request->billing_address;
		$user->billing_address_line_2 = $request->billing_address_line_2;
		$user->billing_city = $request->billing_city;
		$user->billing_state = $request->billing_state;
		$user->billing_zip = $request->billing_zip;
		
		
		
		$shippingAddress = new Shipping_address;
		    
		$shippingAddress->shipping_address = $request->shipping_address;
		$shippingAddress->shipping_address_2 = $request->shipping_address_2;
		$shippingAddress->shipping_city = $request->shipping_city;
		$shippingAddress->shipping_state = $request->shipping_state;
		$shippingAddress->shipping_zip = $request->shipping_zip;
		
	
		 
		
		
		$shippingAddress->save();
	    $user->save();

	    return redirect('/user/' . $id);
	

    }
	
	
	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function updateUser($id, Request $request)
    {

		$validator = Validator::make($request->all(), [
	        'user_name' => 'required|max:255',
		    'user_email' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/user/' . $id)
	            ->withInput()
	            ->withErrors($validator);
	    }

	    $user = User::find($id);
	    $user->name = $request->user_name;
		$user->email = $request->user_email;
		$user->billing_address = $request->billing_address;
		$user->billing_address_line_2 = $request->billing_address_line_2;
		$user->billing_city = $request->billing_city;
		$user->billing_state = $request->billing_state;
		$user->billing_zip = $request->billing_zip;
		
		
		if ($request->shipping_address_id != '') {
			
			$shippingAddress = Shipping_address::find($request->shipping_address_id);
		
		} else {
			
			$shippingAddress = new Shipping_address;
		}
	
		$shippingAddress->user_id = $id;
		$shippingAddress->is_current = 1;
		$shippingAddress->shipping_address = $request->shipping_address;
		$shippingAddress->shipping_address_2 = $request->shipping_address_2;
		$shippingAddress->shipping_city = $request->shipping_city;
		$shippingAddress->shipping_state = $request->shipping_state;
		$shippingAddress->shipping_zip = $request->shipping_zip;
		
		$shippingAddress->save();
		$user->save();
		
		/*check to see if there's a new note */
		if ($request->note_text != '') {
			
			$new_note = new Csr_note;
			$new_note->user_id = $id;
			$new_note->note_text = $request->note_text;
			$new_note->csr_id = 0;
			$new_note->save();
			
		}
		 
		
		
	

	    return redirect()->back();
	

    }

	public function saveCSRNote($id, Request $request) {
		
			/*check to see if there's a new note */
			if ($request->note_text != '') {

				$new_note = new Csr_note;
				$new_note->user_id = $id;
				$new_note->note_text = $request->note_text;
				$new_note->csr_id = 0;
				$new_note->save();

			}
		
		 return redirect()->back();
		
	}

	public function showSubscription($id) {
		
		//get subscriptions (subscriptions table), join Product, join Dietary Preferences 
		
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		
		if ($userSubscription) {
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->firstOrFail();
		}
		
		$user = User::find($id);
		$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();

		
		if ($userSubscription) {
			return view('admin.users.user-subscriptions')->with(['user'=>$user, 'userSubscription'=>$userSubscription, 'csr_notes'=>$csr_notes,'userProduct'=>$userProduct]);
		}else{
			
			//return ("test");
			
		}
		
		
		
	}
	
	public function updateSubscription ($id, Request $request) {
		
		$validator = Validator::make($request->all(), [
	        'plan_type' => 'required|max:255',
		    'plan_size' => 'required|max:1000'
	    ]);

	    if ($validator->fails()) {
	        return redirect('/user/subscriptions/' . $id)
	            ->withInput()
	            ->withErrors($validator);
	    }
	
		/*sku decoder - 
		01 veg/onmivore
		02	num adults
		03	num children (04= 4 children)
		01  Gluten Free (00 is regular with Gluten)
		00	unused
		*/
		
		$plan_type = $request->plan_type;
		$plan_size = $request->plan_size;
		$num_kids = $request->num_children;
		$theSKU = '';
		
		
		if ($plan_type=='vegetarian') {
			$theSKU = "01";
		}
		if ($plan_type=='omnivore') {
			$theSKU = "02";
		}
		
		
		//num adults defaults to 02
		$theSKU .= "02";
		
		if ($plan_size=="family") {
			
			if ($num_kids=="0") {$theSKU .= "00";}
			if ($num_kids=="1") {$theSKU .= "01";}
			if ($num_kids=="2") {$theSKU .= "02";}
			if ($num_kids=="3") {$theSKU .= "03";}
			if ($num_kids=="0") {$theSKU .= "04";}
			
		}else{
			$theSKU .= "00";
		}
		
		$theSKU .= "0000";
		
	
		
		//look up the product ID
		$newProduct = Product::where('sku',$theSKU)->firstOrFail();
		
		$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
		$userSubscription->product_id = $newProduct->id;
		$userSubscription->dietary_preferences = implode(",",$request->prefs);
		$userSubscription->save();
		
		//update STRIPE
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	
		$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
		$subscription->plan = $newProduct->stripe_plan_id;
		$subscription->save();
		
			
			//figure out the sku based on plan_type, plan_size, and number of children

			//update the subscription record with the new plan_type
			
				
		
		 return redirect()->back();
		
	}
	
	public function showPayments($id) {
		
		//get subscriptions (subscriptions table), join Product, join Dietary Preferences 
		
		
		$user = User::find($id);
	
		//return "test";
		return view('admin/users/user-payments')->with(['user'=>$user]);
		
		
		
	}
	
	
	
	public function showReferrals($id) {
		
			$referrals = Referral::where('referrer_user_id',$id)->get();
			$user = User::find($id);
			$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();

			return view('admin/users/user-referrals')->with(['user'=>$user, 'csr_notes'=>$csr_notes, 'referrals'=>$referrals]);
			
					
	}
	
	public function sendReferral($id, Request $request) {
		
			
			$user = User::find($id);
			
			//validate form
			$validator = Validator::make($request->all(), [
		        'send_email' => 'required|max:255|email'
		    ]);

		    if ($validator->fails()) {
		        return redirect('/user/referrals/' . $id)
		            ->withInput()
		            ->withErrors($validator);
		    }
			
			
			
			
			//send email
			$to_send = $request->send_email;
			$custom_message = $request->custom_message;
			
			//record a new referral in the referral table
			$referral = new Referral;
			$referral->referral_email = $to_send;
			$referral->did_subscribe = 0;
			$referral->referrer_user_id = $id;
			
			
			$referral->save();
			
			
			$data = [
			           'friendname' => $user->name,
			           'custommessage' => $custom_message,
					   'to_send' => $to_send,
					   'friendid' => $id,
					   'referralid' => $referral->id
			];
			
			Mail::send('emailtest', $data , function($message) use ($to_send)
			{
				    $message->from('mattkirkpatrick@gmail.com');
				    $message->to($to_send, '')->subject('A Message from a Friend at One Potato!');
			});
		
			//return "test";
			return redirect('/user/referrals/' . $id);
			
					
	}

    public function recordReferral (Request $request) {
			
			//http://onepotato.app/referral/subscribe/?u=mattkirkpatrick@yahoo.com&f=1
			//$id = $request->f;
			$referrerid = $request->f;
			$newuserid = $request->u;
			
			//record that the user has subscribed--this is stubbed in for now
			$referral = Referral::where('id',$newuserid)->firstorfailfs();
			$referral->did_subscribe = 1;
			$referral->save();
			
			//return "test";
			return redirect('/user/referrals/' . $referrerid);
		
	}

	public function showPayment ($id, Request $request) {
		
			$user = User::find($id);
			$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();
			
		
			//return "test";
			return view('admin/users/payment')->with(['user'=>$user, 'csr_notes'=>$csr_notes]);
		
	}
	
	public function savePayment ($id, Request $request) {
		
			$user = User::find($id);
			$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();
			$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->firstOrFail();
			
			//figure out which plan the user is currently subscribed to
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			
			// Get the credit card details submitted by the form
			$token = $request->stripeToken;
			
		
			//first see if there's already a record for this customer in STRIPE and update using Token
			if ($user->stripe_id != '') {
			
				$customer = \Stripe\Customer::retrieve($user->stripe_id);
				$customer->source = $token; // obtained with Stripe.js
				$customer->email = $user->email;
				$customer->plan = $userProduct->stripe_plan_id;
				$customer->save();
				
			}else{
				
					$customer = \Stripe\Customer::create(array(
					  "source" => $token,
					  "plan" => $userProduct->stripe_plan_id,
					  "email" => $user->email)
					);
						
					$user->stripe_id = $customer->id;
				
					$user->save();
				
			}
			return view('admin/users/payment')->with(['user'=>$user, 'csr_notes'=>$csr_notes]);
			
			//NOTE: When a user changes their plan, need to ALSO update that in STRIPE
		
	}
	
	public function showTest ($id) {
		
		
			Mail::send('emailtest', array('key' => 'value'), function($message)
			{
			    $message->from('mattkirkpatrick@gmail.com');
			    $message->to('mattkirkpatrick@gmail.com', 'Matt Kirkpatrick')->subject('Welcome!');
			});
			return "test";
	}
	
	public function showDeliverySchedule () {
		
		$id =  Auth::id();
		$user = User::find($id);
		$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
		$sub = Dietary_preference::where('user_id',$id)->firstOrFail();
		$productID = $userSubscription->product_id;
		$userProduct = Product::where('id',$productID)->firstOrFail();
		$product_type = $userProduct->product_type == 1 ? "isOmnivore" : "isVegetarian";

		$startDate = date('Y-m-d H:i:s');
		//$startDate = date('Y-m-d H:i:s', strtotime("+1 week"));
    	$endDate = date('Y-m-d H:i:s', strtotime("+6 weeks"));
    	$noWeekMenu = [];

    	for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime('+1 day', $i)) {
			if (date('N', $i) == 2) {//Tuesday == 2
    			$deliverySchedule = new stdClass;
				$whatscooking = WhatsCookings::where('week_of',date('Y-m-d', $i))->first();

    			$deliverySchedule->date = date('l, M jS', $i);
    			$deliverySchedule->date2 = date('Y-m-d', $i);
    			$deliverySchedule->date3 = date('F j, Y', $i);
				$deliverySchedule->all = [];
				if (isset($whatscooking)) {
    				//$deliverySchedule->menus = $whatscooking->menus()->where($product_type,1)->get();
    				
    				$deliverySchedule->menus = MenusUsers::where('users_id',$id)
    											->where('delivery_date',date('Y-m-d', $i))
    											->get();
    				$deliverySchedule->all = $whatscooking->menus()->get();
				} else {
    				$deliverySchedule->menus = [];
				}

				$plan_change = Plan_change::where('user_id',$id)->where('date_to_change',date('Y-m-d', $i))->where('status','to_change')->first();
				if (count($plan_change) > 0) {
					$sku = $plan_change->sku_to_change;
					$sku_array = str_split($sku, 2);
					$deliverySchedule->children = (integer)ltrim($sku_array[2], '0');
				} else {
					$deliverySchedule->children = $userProduct->productDetails()->ChildSelect;
				}

				$hold = Shippingholds::where('user_id',$id)
						->where('date_to_hold', date('Y-m-d', $i))
						->where('hold_status', 'hold')
						->get();
				if (count($hold) > 0) $deliverySchedule->hold = true;
				else $deliverySchedule->hold = false;

				$tz = isset($_REQUEST['tz']) ? $_REQUEST['tz'] : 'America/Los_Angeles';
				
				$dt = new \DateTime( date('Y-m-d', $i) );
				$dt->setTimezone(new \DateTimeZone($tz));
				$dt->setTime(9, 00);
				$dt->sub(new \DateInterval('P5D'));
				$dt->format('Y-m-d H:i');

                $now = new \DateTime();
                $now->setTimezone(new \DateTimeZone($tz));
                $now->format('Y-m-d H:i');

                if ($dt->format('Y-m-d H:i') < $now->format('Y-m-d H:i')) $deliverySchedule->changeable = 'no';
                else $deliverySchedule->changeable = 'yes';

                $deliverySchedule->deadline = $dt->format('l, M jS');

				$weeksMenus[] = $deliverySchedule;
			}

    	}

    	$invoices = Subinvoice::where('user_id',$id)->where('invoice_status','sent_to_ship')->first();
		
		$trackingNumber = NULL;
		if (isset($invoices)) $order = Order::where('order_id',$invoices->order_id)->first();
		if (isset($order)) $trackingNumber = $order->tracking_number;

   		//echo json_encode($weeksMenus[0]);
   		//echo json_encode($weeksMenus[0]->menus[0]->menu()->get());
   		return view('delivery_schedule')->with(['userid'=>$id, 'startDate'=>$user->start_date, 'weeksMenus'=>$weeksMenus, 'userProduct'=>$userProduct, 'trackingNumber'=>$trackingNumber, 'prefs'=>$sub->dietary_preferences]);

	}
	
	public function changeDelivery (Request $request) {
		
		$id =  Auth::id();
		$user = User::find($id);

		$menususers = MenusUsers::where('users_id',$id)->where('delivery_date',$request->date_to_change)->get();
		$i = 0;
		foreach ($menususers as $menususer) {
			$menususer->menus_id = $request->menu_id[$i];
			$i++;
			$menususer->save();
		}

		return redirect('/delivery-schedule'); 
	}




	public function adminTest(Request $request) {

        $user = User::where('email', 'agedgouda@gmail.com')->first();

		$x = AC_Mediator::GetInstance();
		var_dump($x->AddNewSubscriber($user));die();

    }


	public function cancelUserAccount(Request $request) {
		
		//permanently deactive an account
		//mark record as cancelled in Users, Subscriptions tables
		$user = User::where('id', $request->user_id)->first();
		$user->status = User::STATUS_INACTIVE_CANCELLED;


		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
		$userSubscription->status = "cancelled";
		
		$stripe_sub_id = $userSubscription->stripe_id;
		
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here https://dashboard.stripe.com/account/apikeys



//TODO:: Uncomment live!!!!!! VERY IMPORTANT !!!!!!

		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				
		$subscription = \Stripe\Subscription::retrieve($stripe_sub_id);
		$subscription->cancel();

		$cancel = new Cancellation();
		
		$cancel->user_id = $request->user_id;
		$cancel->cancel_reason = $request->cancel_reason;
		$cancel->cancel_specify = $request->cancel_specify;
		$cancel->cancel_suggestions = $request->cancel_suggestions;
		$cancel->save();
		
		
		$user->save();
		$userSubscription->save();

        $ac = AC_Mediator::GetInstance();
        try {
            $ac->AddCustomerTag($user, 'Cancellation');
        } catch (\Exception $e) {
            //Do Nothing
        }

        $reactivateMessage = '';
        if($request->lastDeliveryDate) {
            $lastDeliveryDate = new \DateTime($request->lastDeliveryDate);
            $reactivateMessage = "You will receive your final meal delivery on {$lastDeliveryDate->format('l, F jS')}.";
        }

        Auth::logout();

        //By clicking “Reactivate Account,” you agree you are purchasing a continuous subscription and will receive weekly deliveries billed to your designated payment method. You can skip a delivery on our website, or cancel your subscription by contacting us and following the instructions we provide you in our response, on or before the “Changeable By” date reflected in your Account Settings. For more information see our Terms of Use and FAQs.
        return view('account_cancelled')->with([
            'user' => $user,
            'reactivateMessage' => $reactivateMessage,
        ]);

        //Old Behaviour below
		Auth::logout();
		
		//record cancel reason in cancellation table
		return redirect("/logout");
		
	}

    public function reactivateUserAccount(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        $user->status = User::STATUS_ACTIVE;


        //retrieve stripe ID from subscriptions table
        $userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
        $userSubscription->status = "active";

        $stripe_sub_id = $userSubscription->stripe_id;

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://dashboard.stripe.com/account/apikeys


        die("TO BE DONE!!!! Guess need to create new stripe subscription. Matt, what do you think??");

                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $subscription = \Stripe\Subscription::retrieve($stripe_sub_id);
                $subscription->cancel();

        $cancel = new Cancellation();

        $cancel->user_id = $request->user_id;
        $cancel->cancel_reason = $request->cancel_reason;
        $cancel->cancel_specify = $request->cancel_specify;
        $cancel->cancel_suggestions = $request->cancel_suggestions;
        $cancel->save();


        $user->save();
        $userSubscription->save();

        $ac = AC_Mediator::GetInstance();
        try {
            $ac->AddCustomerTag($user, 'Cancellation');
        } catch (\Exception $e) {
            //Do Nothing
        }

        $reactivateMessage = '';
        if($request->lastDeliveryDate) {
            $lastDeliveryDate = new \DateTime($request->lastDeliveryDate);
            $reactivateMessage = "You will receive your final meal delivery on {$lastDeliveryDate->format('l, F jS')}.";
        }

        Auth::logout();

        //By clicking “Reactivate Account,” you agree you are purchasing a continuous subscription and will receive weekly deliveries billed to your designated payment method. You can skip a delivery on our website, or cancel your subscription by contacting us and following the instructions we provide you in our response, on or before the “Changeable By” date reflected in your Account Settings. For more information see our Terms of Use and FAQs.
        return view('account_cancelled')->with([
            'user' => $user,
            'reactivateMessage' => $reactivateMessage,
        ]);

    }

	public function ResolveCancelLink($code) {

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $cryptString = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::CRYPT_KEY, $jsonString, MCRYPT_MODE_ECB, $iv);
//
//        $encodedString = base64_encode($cryptString);


        $urlDecoded = rawurldecode($code);
        $base64Decoded = base64_decode($urlDecoded);
        $decrypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256,
            Cancellation::CRYPT_KEY,
            $base64Decoded,
            MCRYPT_MODE_ECB,
            $iv
        );

        $trimmed = trim($decrypted);

        $data = json_decode($trimmed);

        if(!$data) {
            abort(404, "Invalid link");
            exit;
        }


        $userId = $data->userId;
        $email = $data->userEmail;
        $validTo = new \DateTime($data->validTo);

        $now = new \DateTime('now');

        if($now > $validTo) {
            abort(404, "Link is expired");
        }

        $user = User::find($userId);

        if($user->email != $email) {
            Auth::logout();
            abort(404, 'Something wrong');
        }


        if(!Auth::user()) {
            Auth::logout();
            abort(404, 'Please login');
        }

        if(Auth::user()->id != $user->id) {
            Auth::logout();
            abort(404, 'Wrong user');
        }


//        echo "Going on";

        $currentInvoice = Subinvoice::where('user_id',$user->id)
            ->where('invoice_status','sent_to_ship')
            ->orderBy('charge_date','desc')
            ->first();

        $cancelMessage = '';
        $lastDeliveryDateText = '';
        if (isset($currentInvoice->charge_date)) {

            $chargeDate = new \DateTime($currentInvoice->charge_date);
            $lastDeliveryDate = clone($chargeDate);
            $lastDeliveryDate->modify("next tuesday");
//var_dump($chargeDate);
//var_dump($lastDeliveryDate);die();

//            $lastDeliveryDate = date('l, F jS',strtotime("next tuesday",strtotime($currentInvoice->charge_date)));
            if ( $lastDeliveryDate < $now ) {
                $cancelMessage = "Your final delivery is scheduled for ".$lastDeliveryDate->format('l, F jS')." has already been processed and cannot be changed.";
                $lastDeliveryDateText = $lastDeliveryDate->format('Y-m-d');
            }
        }



        return view('account_cancel')->with([
            'user'=>$user,
            'cancelMessage' => $cancelMessage,
            'lastDeliveryDate' => $lastDeliveryDateText,
        ]);
    }

/*
object(stdClass)[456]
  public 'junk1' => int 347368
  public 'userId' => int 2635
  public 'junk2' => int 484985
  public 'userEmail' => string 'agedgouda@gmail.com' (length=19)
  public 'junk3' => int 28277
  public 'validTo' => string '2016-09-20 09:31:11' (length=19)
  public 'junk4' => int 884217
     */

}

