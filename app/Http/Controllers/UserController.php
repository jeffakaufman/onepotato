<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;

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
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        return view('user');
    }

	
 	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showUsers()
    {
        	$users = User::get();
			return view('users')->with(['users'=>$users]);
    }

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showUser($id)
    {

		$user = User::find($id);
		$shippingAddresses = Shipping_address::where('user_id',$id)->orderBy('is_current', 'desc')->get();
		$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();
		
		return view('user')->with(['user'=>$user, 'shippingAddresses'=>$shippingAddresses, 'csr_notes'=>$csr_notes]);

    }

	public function newUser() {
		
		return view('newuser');
		
	}

	/**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function createUser(Request $request)
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
		
		$userSubscription = UserSubscription::where('user_id',$id)->firstOrFail();
		$productID = $userSubscription->product_id;
		$userProduct = Product::where('id',$productID)->firstOrFail();
		$user = User::find($id);
		$csr_notes = Csr_note::where('user_id',$id)->orderBy('created_at', 'desc')->get();
		
	
		
		//return "test";
		return view('user-subscriptions')->with(['user'=>$user, 'userSubscription'=>$userSubscription, 'csr_notes'=>$csr_notes,'userProduct'=>$userProduct]);
		
		
		
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
		00  unused
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
			
			//figure out the sku based on plan_type, plan_size, and number of children

			//update the subscription record with the new plan_type
			
				
		
		 return redirect()->back();
		
	}
	
	public function showPayments($id) {
		
		//get subscriptions (subscriptions table), join Product, join Dietary Preferences 
		
		
		$user = User::find($id);
	
		//return "test";
		return view('user-payments')->with(['user'=>$user]);
		
		
		
	}
    
	
	
}
