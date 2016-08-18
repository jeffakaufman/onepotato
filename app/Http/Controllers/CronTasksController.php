<?php

namespace App\Http\Controllers;

use App\Events\UserHasRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;
use App\Referral;
use App\ZipcodeStates;
use App\MenusUsers;
use App\Plan_change;
use App\Shippingholds;
use Hash;
use Mail;
use DateTime;
use DateTimeZone;
use Session;
use Auth;

class CronTasksController extends Controller
{
	
	public function RunTasks () {
		
		$this->CheckForPlanChanges();
		
		//$this->ChekkForHolds();
		
	}
	
	public function CheckForPlanChanges () {
		
		//something like this - 
		
		//get all the "to_change" statuses from Plan_changes
		
		//statuses: "to_change" - new tochange, "was_changed" = old to change back, "inactive" - done and done
	
		//last Tuesday: for plans to change back - this must run BEFORE the new changes in case 
		//				a user has changed a plan two weeks in a row
		
		$last_week_of = new DateTime();
		$last_week_of->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$last_week_of->modify('last Tuesday');
		$last_week_of_date = $last_week_of->format('Y-m-d');
		
		$old_plan_changes = Plan_change::where('status','was_changed')
										->where('date_to_change', $last_week_of_date)->get();
		
	
		foreach ($old_plan_changes as $old_plan_change) {
			
			//change plan back
			
			//change status to "inactive"
			$id = $old_plan_change->user_id;
			$new_sku = $old_plan_change->old_sku;
			
			//look up the user
			$userSubscription = UserSubscription::where('user_id',$id)->first();
			
			//now get the product ID for the new SKU
			$newProduct = Product::where('sku', $new_sku)->first();
			
			//update the user subscription
			$userSubscription->product_id = $newProduct->id;
			$userSubscription->save();
			
			//update STRIPE
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
			$subscription->plan = $newProduct->stripe_plan_id;
			$subscription->prorate = "false";
			$subscription->save();
			
			//change the status of the plan
			$old_plan_change->status = "inactive";
			$old_plan_change->save();
			
			
			
		}
		
		
		
		//this Tuesday: for plans to change THIS week
		$next_week_of = new DateTime();
		$next_week_of->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$next_week_of->modify('this Tuesday ');
		$this_week_of_date = $next_week_of->format('Y-m-d');
		
		$plan_changes =  Plan_change::where('status','to_change')
										->where('date_to_change', $this_week_of_date)->get();
		
		foreach ($plan_changes as $plan_change) {
			
			$id = $plan_change->user_id;
			$new_sku = $plan_change->sku_to_change;
			
			//look up the user
			$userSubscription = UserSubscription::where('user_id',$id)->first();
			
			//now get the product ID for the new SKU
			$newProduct = Product::where('sku', $new_sku)->first();
			
			//update the user subscription
			$userSubscription->product_id = $newProduct->id;
			$userSubscription->save();
			
			//update STRIPE
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
			$subscription->plan = $newProduct->stripe_plan_id;
			$subscription->prorate = "false";
			$subscription->save();
			
			//change the status of the plan
			$plan_change->status = "was_changed";
			$plan_change->save();
			
			
		}
		
		/*
		//look up the product ID

		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$current_product_id = $userSubscription->product_id;
		
		//lookup the SKU
		
		$currentProduct = Product::where('id', $current_product_id)->first();
		$current_sku = $currentProduct->sku;
		
		//update the sku - 
		$sku_array = str_split($current_sku, 2);
		
		//update the third position (2) to new number of children
		$sku_array[2] = "0" . $num_children;
		
		$new_sku = implode ("",$sku_array);
		
		
		//now get the product ID for the new SKU
		$newProduct = Product::where('sku', $new_sku)->first();
		
		//update the user subscription
		$userSubscription->product_id = $newProduct->id;
		$userSubscription->save();


		//update STRIPE
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

		$subscription = \Stripe\Subscription::retrieve($userSubscription->stripe_id );
		$subscription->plan = $newProduct->stripe_plan_id;
		$subscription->prorate = "false";
		$subscription->save();
		
		*/
		
		//return success code
		http_response_code(200);
		
		
	}
		
	public function CheckForHolds () {}
	
}