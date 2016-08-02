<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;
use App\Referral;
use Mail;
use Hash;
use Auth;
use CountryState;
use App\WhatsCookings;
use App\Menus;
use stdClass;

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
        	$users = User::get();
			return view('admin.users.users')->with(['users'=>$users]);
    }

	public function getAccount($id = null) {
		
		$id = !isset($id) ? Auth::id() : $id;
		
		
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

		return view('account')
					->with(['user'=>$user, 
							'shippingAddress'=>$shippingAddress, 
							'userSubscription'=>$userSubscription, 
							'userProduct'=>$userProduct, 
							'states'=>$states,
							'referrals'=>$referrals]);
		
		
	
	}

	public function editAccount(Request $request) {
		
			//get all the user objects and pass to the view
			$user = User::find($request->user_id);
			$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
			$states = CountryState::getStates('US');
			$shippingAddress = Shipping_address::where('user_id',$request->user_id)->orderBy('is_current', 'desc')->first();
			$referrals = Referral::where('referrer_user_id',$request->user_id)->get();
			

			if ($userSubscription) {
				$productID = $userSubscription->product_id;
				$userProduct = Product::where('id',$productID)->first();
			}

			$update_type = $request->update_type;
			
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
			$userSubscription->dietary_preferences = implode(",",$request->prefs);
			$userSubscription->save();

			//update STRIPE
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id);
			$subscription->plan = $newProduct->stripe_plan_id;
			$subscription->save();

			}
			//end meal/plan update
			
 
			return view('account')
						->with(['user'=>$user, 
								'shippingAddress'=>$shippingAddress, 
								'userSubscription'=>$userSubscription, 
								'userProduct'=>$userProduct, 
								'states'=>$states,
								'referrals'=>$referrals]);
		
		
		
		
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
		$productID = $userSubscription->product_id;
		$userProduct = Product::where('id',$productID)->firstOrFail();
		
		$product_type = $userProduct->product_type == 2 ? "Omnivore" : "Vegetarian";
		
		$startDate = date('Y-m-d H:i:s', strtotime("+1 week"));
    	$endDate = date('Y-m-d H:i:s', strtotime("+6 weeks"));
    	$noWeekMenu = [];

    	for ($i = strtotime($startDate); $i <= strtotime($endDate); $i = strtotime('+1 day', $i)) {
			if (date('N', $i) == 2) {//Tuesday == 2
    			$deliverySchedule = new stdClass;
				$whatscooking = WhatsCookings::where('week_of',date('Y-m-d', $i))
								->where('product_type',$product_type)
								->first();
    			$deliverySchedule->date = date('l, M jS', $i);
				
				if (isset($whatscooking)) {
    				$deliverySchedule->menus = $whatscooking->menus()->get();
				} else {
    				$deliverySchedule->menus = [];
				}
				$weeksMenus[] = $deliverySchedule;
			}   
    	}
   	return view('delivery_schedule')->with(['weeksMenus'=>$weeksMenus, 'userProduct'=>$userProduct]);

	}
	
	
	
}
