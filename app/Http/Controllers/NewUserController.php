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
use Hash;
use Mail;

class NewUserController extends Controller
{
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
		
		//add name to this
		
		//create a new user w form data from 
		//validation
		$validator = Validator::make($request->all(), [
		      
			    'email' => 'required|email|max:1000|unique:users',
				'password' => 'required|max:255|same:password_confirmation',
				'zip' => 'required|digits:5',
				
		]);

		if ($validator->fails()) {
		        return redirect('/register')
		            ->withInput()
		            ->withErrors($validator);
		}

		$user = new User;
	
		$user->email = $request->email;
		$user->password = Hash::make($request->password);
		$user->save();
		
		return view('register.select_plan')->with(['user'=>$user]);
	}
	
	public function RecordPlan (Request $request) {
		
		//send the plan data straight to the view
		$numChildren = $request->children;
		$user = User::find($request->user_id);
		
		return view('register.preferences')->with(['children'=>$numChildren,'user'=>$user]);
		
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
		
		if ($plan_type=='vegetarian') {
			$theSKU = "01";
		}
		if ($plan_type=='omnivore') {
			$theSKU = "02";
		}
		
		
		//num adults defaults to 02
		$theSKU .= "02";
	
			
		if ($num_kids=="0") {$theSKU .= "00";}
		if ($num_kids=="1") {$theSKU .= "01";}
		if ($num_kids=="2") {$theSKU .= "02";}
		if ($num_kids=="3") {$theSKU .= "03";}
		if ($num_kids=="4") {$theSKU .= "04";}
			
	
		if ($request->glutenfree) {
			$theSKU .= "0100";
		}else{
			$theSKU .= "0000";
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
		
		//update STRIPE
		/*
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	
		$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
		$subscription->plan = $newProduct->stripe_plan_id;
		$subscription->save();
		*/
		
		$user = User::find($request->user_id);
	
		
		return view('register.delivery')->with(['user'=>$user]);
	}
		
	public function RecordDeliveryPreferences (Request $request) {
		
		//store first and last name in User field
		$user = User::find($request->user_id);
		
		//store shipping address
		$shippingAddress = new Shipping_address;
		$shippingAddress->shipping_first_name = $request->firstname;
		$shippingAddress->shipping_last_name = $request->lastname;
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
		
		if ($request->delivery_loc=="Home") {$shippingAddress->address_type="home";}
		if ($request->delivery_loc=="Business") {$shippingAddress->address_type="business";}
		
		$user->name = $request->firstname . " " . $request->lastname;
		
		//add - home/business
		//add - delivery instructions

		$shippingAddress->save();
		$user->save();
		
		//store children's birthdays
		//add - children's birthdays
		
		//take them to the next step!
		
		return view('register.payment')->with(['user'=>$user]);
		
	}
		
	public function RecordPayment (Request $request) {
		
		
			//validation errors
		
			$user = User::find($request->user_id);
			$userSubscription = UserSubscription::where('user_id',$request->user_id)->first();
			$productID = $userSubscription->product_id;
			$userProduct = Product::where('id',$productID)->first();
			
			//figure out which plan the user is currently subscribed to
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			
			// Get the credit card details submitted by the form
			$token = $request->stripeToken;
				
			$customer = \Stripe\Customer::create(array(
					"source" => $token,
					"plan" => $userProduct->stripe_plan_id,
					"email" => $user->email)
			);
						
			$user->stripe_id = $customer->id;
			
			//get the subscription ID
			$userSubscription->stripe_id = $customer->subscriptions->data[0]->id;
			
			//update statuses to "active"
			//return errors if CC didn't go through
			
			//record billing address
			$user->billing_address = $request->address;
			$user->billing_address_line_2 = $request->address_2;
			$user->billing_city =  $request->city;
			$user->billing_state =  $request->state;
			$user->billing_zip =  $request->zip;
			$user->billing_country = "US";
			$user->phone =  $request->phone;
			
			$userSubscription->save();
			$user->save();
				
			return view('register.congrats')->with(['user'=>$user]);
			
	}
		



}